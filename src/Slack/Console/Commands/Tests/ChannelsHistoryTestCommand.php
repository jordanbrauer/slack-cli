<?php

namespace Slack\Console\Commands\Tests;

use Slack\Console\Commands\ChannelsHistoryCommand;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester as TestCommand;

/**
 * Class ChannelsHistoryTestCommand
 * @package Slack\Console\Commands\Tests
 */
class ChannelsHistoryTestCommand
{
    /**
     * @param array $parameters
     * @return mixed
     */
    public function execute(array $parameters = array())
    {
        $application = new Application("Slack CLI", "0.0.1");

        $application->add(new ChannelsHistoryCommand);

        $command = $application->find("channel:history");
        $parameters["command"] = $command->getName();

        $testCommand = new TestCommand($command);
        $testCommand->execute($parameters);

        return $testCommand->getDisplay();
    }
}
