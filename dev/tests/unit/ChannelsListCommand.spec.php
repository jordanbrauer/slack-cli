<?php

namespace Slack\Tests;

use Dotenv\Dotenv;
use PHPUnit\Framework\TestCase;
use Slack\Api\Client;
use Slack\Tests\Commands\ChannelsListTestCommand;

class ChannelsListCommandSpec extends TestCase
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

    $this->command = new ChannelsListTestCommand;
  }

  protected function tearDown ()
  {
    $env = null;
    $this->client = null;
    $this->command = null;
  }

  public function test_decoded_json_output ()
  {
    $decodedJson = json_decode($this->command->execute(["--output" => "json"]));

    $this->assertInternalType("object", $decodedJson);
    $this->assertInternalType("array", $decodedJson->channels);
    $this->assertInternalType("object", $decodedJson->channels[0]);
  }

  public function test_json_output ()
  {
    # NOTE: trim surrounding newline characters ('\n') for string assertions
    $jsonOutput = trim($this->command->execute(["--output" => "json", "--pretty" => 0]), "\n ");

    $this->assertInternalType("string", $jsonOutput);

    $this->assertStringStartsWith("{", $jsonOutput);
    $this->assertStringEndsWith("}", $jsonOutput);

    $this->assertStringStartsNotWith("+", $jsonOutput);
    $this->assertStringEndsNotWith("+", $jsonOutput);
  }

  public function test_table_output ()
  {
    # NOTE: trim surrounding newline characters ('\n') for string assertions
    $tableOutput = trim($this->command->execute(["--output" => "table"]), "\n ");

    $this->assertInternalType("string", $tableOutput);

    $this->assertStringStartsWith("+", $tableOutput);
    $this->assertStringEndsWith("+", $tableOutput);

    $this->assertStringStartsNotWith("{", $tableOutput);
    $this->assertStringEndsNotWith("}", $tableOutput);
  }

  public function test_excluded_members_collection ()
  {
    $channelObject = json_decode($this->command->execute([
      "--output" => "json",
      "--exclude_members" => 1
    ]))->channels[0];

    $this->assertObjectNotHasAttribute("members", $channelObject);
  }

  public function test_included_members_collection ()
    {
    $channelObject = json_decode($this->command->execute([
      "--output" => "json",
      "--exclude_members" => 0
    ]))->channels[0];

    $this->assertObjectHasAttribute("members", $channelObject);
  }
}
