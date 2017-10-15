<?php

namespace Slack\Console\Commands;

use Dotenv\Dotenv;
use Slack\Api\Client;
use Slack\Api\Entities\MessageEntity as Message;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;

/**
 * Class ChannelsHistoryCommand
 * @package Slack\Console\Commands
 */
class ChannelsHistoryCommand extends Command
{
    /**
     *
     */
    protected function configure()
    {
        $this
            ->setName("channels:history")
            ->setAliases(["channel:history"])
            ->setDescription("This method returns a portion of message events from the specified public channel.")
            ->setHelp("To read the entire history for a channel, call the method with no latest or oldest arguments, and then continue paging using the instructions below.\nTo retrieve a single message, specify its ts value as latest, set inclusive to true, and dial your count down to 1.")
            # Arguments

            ->addArgument("channel", InputArgument::REQUIRED, "The channel that a message is being deleted from")
            # Options

            ->addOption("count", null, InputOption::VALUE_REQUIRED, "Amount of messages to fetch from the specified channel", 100)
            ->addOption("output", "o", InputOption::VALUE_REQUIRED, "Output type for this command. Possible values include: table, json, timestamps", "table")
            ->addOption("pretty", null, InputOption::VALUE_REQUIRED, "Print the JSON response body in preformatted text", 0);
    }

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     * @return mixed
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        # Styled IO
        $io = new SymfonyStyle($input, $output);

        # Load environment
        $env = new Dotenv(__DIR__ . "/../../../../");
        $env->load();

        # Create client
        $client = new Client([
            "base_url" => "https://slack.com/api",
            "token"    => getenv("SLACK_API_TOKEN")
        ]);

        # Request method
        $request = $client->ping("channels.history", [
            "channel" => $input->getArgument("channel"),
            "count"   => $input->getOption("count"),
            "pretty"  => $input->getOption("pretty"),
        ]);

        # Response object (mostly syntactical sugar)
        $response = (object)[
            "code" => $request->getStatusCode(),
            "body" => $request->getBody(),
            "ok"   => json_decode($request->getBody())->ok,
        ];

        # Debug information
        if ($io->isVerbose()):
            $io->text("<options=bold,underscore>Debug:</>");

            $io->text("URL Method: <comment>{$client->getMethod()}</comment>");
            $io->text("Channel: <comment>{$input->getArgument('channel')}</comment>");

            if ($io->isVeryVerbose()):
                $io->text("Count: <comment>{$input->getOption('count')}</comment>");
                $io->text("Pretty JSON: <comment>{$input->getOption('pretty')}</comment>");
                $io->text("Output Style: <comment>{$input->getOption('output')}</comment>");
            endif;

            if ($io->isDebug()):
                $io->text("Response Code: <fg=cyan>{$response->code}</>");
            endif;

            $io->newLine();
        endif;

        # Output based on response code and ok status
        if ($response->code != 200):
            return $io->error([$response->code, $response->body]);
        elseif ($response->code == 200):
            if (!$response->ok):
                return $io->error([$response->code, $response->body]);
            else:
                switch ($input->getOption("output")):
                    case "json":
                        return Message::getJsonOutput($io, $response->body);
                        break;
                    case "table":
                        # JSON Serializer and object normalizer (see Symfony serializer component)
                        $serializer = new Serializer(
                            array(new ObjectNormalizer),
                            array(new JsonEncoder)
                        );

                        # Deserialize each channel object and push them into an array
                        $messages = array();
                        foreach (json_decode($response->body)->messages as $messageaSet):
                            $message = $serializer->deserialize(json_encode($messageaSet), Message::class, "json");

                            array_push($messages, $message);
                        endforeach;

                        // $messages = Collection::unserializedJson(json_decode($response->body)->messages, Message::class);

                        # Render the table output
                        return Message::getTableOutput($output, $messages);
                        break;
                    case "timestamps":
                        $messages = json_decode($response->body)->messages;

                        $messageTimestamps = array();
                        foreach ($messages as $message):
                            array_push($messageTimestamps, $message->ts);
                        endforeach;

                        $jsonTimestamps = $input->getOption("pretty") ?
                            json_encode($messageTimestamps, JSON_PRETTY_PRINT) : json_encode($messageTimestamps);

                        return $io->text($jsonTimestamps);
                        break;
                    default:
                        return $io->text($response->body);
                endswitch;
            endif;
        endif;
    }
}
