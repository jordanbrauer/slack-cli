<?php

namespace Slack\Tests\Commands;

use Slack\Commands\ChannelsListCommand;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;

class ChannelsListCommandTest
{
  public function execute (array $parameters = array())
  {
    $application = new Application("Slack CLI", "0.0.1");

    $application->add(new ChannelsListCommand);

    $command = $application->find("channels:list");
    $parameters["command"] = $command->getName();

    $commandTester = new CommandTester($command);
    $commandTester->execute($parameters);

    return $commandTester->getDisplay();
  }
}
