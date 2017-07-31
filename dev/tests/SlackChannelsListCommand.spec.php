<?php

namespace Slack\Tests;

use Dotenv\Dotenv;
use PHPUnit\Framework\TestCase;
use Slack\Api\Client;
use Slack\Tests\Commands\ChannelsListCommandTest;

class SlackChannelsListCommandSpec extends TestCase
{
  protected $client;
  protected $command;

  protected function setUp ()
  {
    $env = new Dotenv(__DIR__."/../../");
    $env->load();

    $this->client = new Client([
      "base_url" => "https://slack.com/api/",
      "token" => getenv("SLACK_API_TOKEN"),
    ]);

    $this->command = new ChannelsListCommandTest;
  }

  protected function tearDown ()
  {
    $env = null;
    $this->client = null;
    $this->command = null;
  }

  public function test_formatted_json_output ()
  {
    $uglyJsonString = trim($this->command->execute(["--output" => "json", "--pretty" => 0]), "\n ");

    $this->assertInternalType("string", $uglyJsonString);
    $this->assertStringStartsWith("{", $uglyJsonString);
    $this->assertStringEndsWith("}", $uglyJsonString);

    $prettyJsonString = trim($this->command->execute(["--output" => "json", "--pretty" => 1]), "\n ");

    $this->assertInternalType("string", $prettyJsonString);
    $this->assertStringStartsWith("{", $prettyJsonString);
    $this->assertStringEndsWith("}", $prettyJsonString);
  }

  public function test_decoded_json_output ()
  {
    $decodedJson = json_decode($this->command->execute(["--output" => "json"]));

    $this->assertInternalType("object", $decodedJson);
    $this->assertInternalType("array", $decodedJson->channels);
  }
}
