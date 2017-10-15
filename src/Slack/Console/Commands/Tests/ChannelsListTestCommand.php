<?php

namespace Slack\Console\Commands\Tests;

use Slack\Console\Commands\ChannelsListCommand;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester as TestCommand;

/**
 * Class ChannelsListTestCommand
 * @package Slack\Console\Commands\Tests
 */
class ChannelsListTestCommand
{
    /**
     * @param array $parameters
     * @return mixed
     */
    public function execute(array $parameters = array())
    {
        $application = new Application("Slack CLI", "0.0.1");

        $application->add(new ChannelsListCommand);

        $command = $application->find("channels:list");
        $parameters["command"] = $command->getName();

        $testCommand = new TestCommand($command);
        $testCommand->execute($parameters);

        return $testCommand->getDisplay();
    }
}
