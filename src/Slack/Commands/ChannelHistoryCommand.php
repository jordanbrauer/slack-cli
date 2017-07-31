<?php

namespace Slack\Commands;

use Dotenv\Dotenv;
use Slack\Api\Client;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Console\Helper\ProgressBar;

class ChannelHistoryCommand extends Command
{
  protected function configure ()
  {
    $this
      ->setName("channel:history")

      ->setDescription("This method returns a portion of message events from the specified public channel.")

      ->setHelp("To read the entire history for a channel, call the method with no latest or oldest arguments, and then continue paging using the instructions below.\nTo retrieve a single message, specify its ts value as latest, set inclusive to true, and dial your count down to 1.")

      # Arguments

      ->addArgument("channel", InputArgument::REQUIRED, "The channel that a message is being deleted from")

      # Options

      ->addOption("count", null, InputOption::VALUE_REQUIRED, "Amount of messages to fetch from the specified channel", 100)

      ->addOption("pretty", null, InputOption::VALUE_REQUIRED, "Print the JSON response body in preformatted text", 0)
      ;
  }

  protected function execute(InputInterface $input, OutputInterface $output)
  {
    $io = new SymfonyStyle($input, $output);

    $env = new Dotenv(__DIR__."/../../../");
    $env->load();

    $client = new Client([
      "base_url" => "https://slack.com/api",
      "token" => getenv("SLACK_API_TOKEN")
    ]);

    $request = $client->ping("channels.history", [
      "channel" => $input->getArgument("channel"),
      "count" => $input->getOption("count"),
      "pretty" => $input->getOption("pretty"),
    ]);

    $response = (object) [
      "code" => $request->getStatusCode(),
      "body" => $request->getBody(),
    ];

    $responseBodyDecoded = json_decode($response->body);

    $messageTimestamps = array();
    foreach ($responseBodyDecoded->messages as $message):
      array_push($messageTimestamps, $message->ts);
    endforeach;

    if ($response->code == 200):
      return $io->text(json_encode($messageTimestamps));
    else:
      $io->error([$response->code, $response->body]);
    endif;
  }
}
