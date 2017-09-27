<?php

namespace Slack\Console\Commands;

use Slack\Api\Client;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Output\BufferedOutput;
use Symfony\Component\Console\Output\NullOutput;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Console\Helper\ProgressBar;

class ChatDeleteBatchCommand extends Command
{
  protected function configure ()
  {
    $this
      ->setName("chat:delete:batch")
      ->setDescription("This method deletes a message from a channel")
      ->setHelp("The response includes the channel and timestamp properties of the deleted message")

      ->addArgument("channel", InputArgument::REQUIRED, "The channel that a message is being deleted from")

      ->addOption("count", null, InputOption::VALUE_REQUIRED, "Amount of messages to fetch from the specified channel", 100)
      ->addOption("rate", null, InputOption::VALUE_REQUIRED, "Time in between request to the Slack API", 2)
      ->addOption("pretty", null, InputOption::VALUE_REQUIRED, "Print the JSON response body in preformatted text", 0)
      ;
  }

  protected function execute(InputInterface $input, OutputInterface $output)
  {
    $io = new SymfonyStyle($input, $output);

    $channelHistory = $this->getApplication()->find("channel:history");
    $chatDelete = $this->getApplication()->find("chat:delete");

    $channelHistoryArgs = array(
      "command" => "channel:history",
      "channel" => $input->getArgument("channel"),
      "--count" => $input->getOption("count"),
      "--output" => "timestamps",
    );

    $channelHistoryInput = new ArrayInput($channelHistoryArgs);
    $channelHistoryOutput = new BufferedOutput;

    $channelHistoryReturnCode = $channelHistory->run($channelHistoryInput, $channelHistoryOutput);

    $channelHistoryOutputText = json_decode($channelHistoryOutput->fetch());

    $io->text("Building argument sets from channel:history output ...");
    $chatDeleteArgSets = array();
    foreach ($channelHistoryOutputText as $timestamp):
      array_push($chatDeleteArgSets, [
        "command" => "chat:delete",
        "channel" => $input->getArgument("channel"),
        "timestamp" => $timestamp,
        "--pretty" => $input->getOption("pretty"),
      ]);
    endforeach;

    $io->text("Building input sets from argument sets ...");
    $chatDeleteInputSets = array();
    foreach ($chatDeleteArgSets as $argSet):
      array_push($chatDeleteInputSets, new ArrayInput($argSet));
    endforeach;

    $io->text("Running chat:delete, using constructed input sets ...");

    $progress = new ProgressBar($output, count($chatDeleteInputSets));
    $progress->setOverwrite(true);
    $progress->setFormat('debug');
    $progress->start();

    // $chatDeleteReturnCodes = array();
    foreach ($chatDeleteInputSets as $inputSet):
      $progress->clear();
      $chatDelete->run($inputSet, $output);
      $progress->display();
      $progress->advance();

      sleep($input->getOption("rate"));
    endforeach;

    $progress->finish();

    $io->newline(2);
  }
}
