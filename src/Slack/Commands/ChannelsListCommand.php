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

class ChannelsListCommand extends Command
{
  protected function configure ()
  {
    $this
      ->setName("channels:list")
      ->setDescription("This method returns a list of channels for your team.")
      ->setHelp("Simply call the command to get a response.")

      // ->addArgument("channel", InputArgument::REQUIRED, "The channel that a message is being deleted from")

      // ->addOption("count", null, InputOption::VALUE_REQUIRED, "Amount of messages to fetch from the specified channel", 100)
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

    $request = $client->ping("channels.list", [
      "pretty" => $input->getOption("pretty"),
    ]);

    $response = (object) [
      "code" => $request->getStatusCode(),
      "body" => $request->getBody(),
    ];

    $responseBodyDecoded = json_decode($response->body);

    $channels = array();
    foreach ($responseBodyDecoded->channels as $channel):
      array_push($channels, $channel);
    endforeach;

    if ($response->code == 200):
      if ($input->getOption("pretty") == 1):
        return $io->text(json_encode($channels, JSON_PRETTY_PRINT));
      else:
        return $io->text(json_encode($channels));
      endif;
    else:
      $io->error([$response->code, $response->body]);
    endif;
  }
}
