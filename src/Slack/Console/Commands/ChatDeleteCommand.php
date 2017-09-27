<?php

namespace Slack\Console\Commands;

use Dotenv\Dotenv;
use Slack\Api\Client;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Console\Helper\ProgressBar;

class ChatDeleteCommand extends Command
{
  protected function configure ()
  {
    $this
      ->setName("chat:delete")
      ->setDescription("This method deletes a message from a channel")
      ->setHelp("The response includes the channel and timestamp properties of the deleted message")

      ->addArgument("channel", InputArgument::REQUIRED, "The channel that a message is being deleted from")
      ->addArgument("timestamp", InputArgument::REQUIRED, "The unix timestamp of the message being deleted")

      ->addOption("pretty", null, InputOption::VALUE_REQUIRED, "Print the JSON response body in preformatted text", 0)
      ;
  }

  protected function execute(InputInterface $input, OutputInterface $output)
  {
    $io = new SymfonyStyle($input, $output);

    $env = new Dotenv(__DIR__."/../../../../");
    $env->load();

    $client = new Client([
      "base_url" => "https://slack.com/api",
      "token" => getenv("SLACK_API_TOKEN"),
    ]);

    $request = $client->ping("chat.delete", [
      "channel" => $input->getArgument("channel"),
      "ts" => $input->getArgument("timestamp"),
      "pretty" => $input->getOption("pretty"),
    ]);

    $response = (object) [
      "code" => $request->getStatusCode(),
      "body" => $request->getBody(),
    ];

    if ($response->code >= 200 && $response->code <= 400):
      $io->text(["<info>{$response->code}</info>", "<comment>{$response->body}</comment>"]);
    else:
      $io->error([$response->code, $response->body]);
    endif;
  }
}
