<?php

/**
 * @author Jordan Brauer <info@jordanbrauer.ca>
 * @version 0.0.1
 */

declare (strict_types = 1);

namespace Slack\Commands;

use Dotenv\Dotenv;
use Slack\Api\Client;
use Slack\Entities\ChannelEntity as Channel;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;

class ChannelsListCommand extends Command
{
  protected function configure ()
  {
    $this
      ->setName("channels:list")

      ->setDescription("This method returns a list of all channels in the team.")

      ->setHelp("Channels included are all channels that the caller is in, channels they are not currently in, and archived channels but does not include private channels. The number of (non-deactivated) members in each channel is also returned.\n\nTo retrieve a list of private channels, use the command groups:list\n\nHaving trouble getting an HTTP 200 response from this command? Try excluding the members list from each channel object using the --exclude_members=0 flag.")

      # Options

      ->addOption("pretty", "p", InputOption::VALUE_REQUIRED, "Print the JSON response body in preformatted text", 0)

      ->addOption("output", "o", InputOption::VALUE_REQUIRED, "Output type for this command. Possible values include: table, json", "table")

      ->addOption("exclude_archived", null, InputOption::VALUE_REQUIRED, "Exclude archived channels from the listing.", 1)

      ->addOption("exclude_members", null, InputOption::VALUE_REQUIRED, "Exclude the members collection from each channel", 1)
      ;
  }

  protected function execute(InputInterface $input, OutputInterface $output)
  {
    # Styled IO
    $io = new SymfonyStyle($input, $output);

    # Load environment
    $env = new Dotenv(__DIR__."/../../../");
    $env->load();

    # Create client
    $client = new Client([
      "base_url" => "https://slack.com/api",
      "token" => getenv("SLACK_API_TOKEN")
    ]);

    # Request method
    $request = $client->ping("channels.list", [
      "pretty" => $input->getOption("pretty"),
      "exclude_archived" => $input->getOption("exclude_archived"),
      "exclude_members" => $input->getOption("exclude_members"),
    ]);

    // $body = json_decode($request->getBody()->getContents())->ok;
    // var_dump($body);
    # Response object (mostly syntactical sugar)
    $response = (object) [
      "code" => $request->getStatusCode(),
      "body" => $request->getBody()->getContents(),
      "ok" => json_decode($request->getBody()->getContents())["ok"],
    ];

    # Debug information
    if ($io->isVerbose()):
      $io->text("<options=bold,underscore>Debug:</>");

      $io->text("URL Method: <comment>{$client->getMethod()}</comment>");

      if ($io->isVeryVerbose()):
        $io->text("Pretty JSON: <comment>{$input->getOption('pretty')}</comment>");
        $io->text("Excluding Archived Channels: <comment>{$input->getOption('exclude_archived')}</comment>");
        $io->text("Excluding Members: <comment>{$input->getOption('exclude_members')}</comment>");
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
      if ($response->ok === false):
        return $io->error([$response->code, $response->body]);
      else:
        switch ($input->getOption("output")):
          case "json":
            return Channel::getJsonOutput($io, $response->body);
            break;
          case "table":
            # JSON Serializer and object normalizer (see Symfony serializer component)
            $serializer = new Serializer(
              array(new ObjectNormalizer),
              array(new JsonEncoder)
            );

            # Deserialize each channel object and push them into an array
            $channels = array();
            foreach (json_decode($response->body)->channels as $channelDataSet):
              $channel = $serializer->deserialize(json_encode($channelDataSet), Channel::class, "json");

              array_push($channels, $channel);
            endforeach;

            // $channels = Collection::unserializedJson(json_decode($response->body)->messages, Channel::class);

            # Render the table output
            return Channel::getTableOutput($output, $channels, [
              "getMembers" => [$input->getOption("exclude_members")],
              "getTopic" => [0],
              "getPurpose" => [0],
            ]);
            break;
          default:
            return $io->text($response->body);
        endswitch;
      endif;
    endif;
  }
}
