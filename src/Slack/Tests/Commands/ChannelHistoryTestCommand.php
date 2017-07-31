<?php

namespace Slack\Tests\Commands;

use Slack\Commands\ChannelHistoryCommand;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester as TestCommand;

class ChannelHistoryTestCommand
{
  public function execute (array $parameters = array())
  {
    $application = new Application("Slack CLI", "0.0.1");

    $application->add(new ChannelHistoryCommand);

    $command = $application->find("channel:history");
    $parameters["command"] = $command->getName();

    $testCommand = new TestCommand($command);
    $testCommand->execute($parameters);

    return $testCommand->getDisplay();
  }
}