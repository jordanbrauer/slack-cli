<?php

namespace Slack\Tests;

use Dotenv\Dotenv;
use PHPUnit\Framework\TestCase;
use Slack\Api\Client;
use Slack\Tests\Commands\ChannelHistoryTestCommand;

class ChannelHistoryCommandSpec extends TestCase
{
  protected $client;
  protected $command;

  protected function setUp ()
  {
    $env = new Dotenv(__DIR__."/../../../");
    $env->load();

    $this->client = new Client([
      "base_url" => "https://slack.com/api/",
      "token" => getenv("SLACK_API_TOKEN"),
    ]);

    $this->command = new ChannelHistoryTestCommand;
  }

  protected function tearDown ()
  {
    $env = null;
    $this->client = null;
    $this->command = null;
  }
}
