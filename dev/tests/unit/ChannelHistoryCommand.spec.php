<?php

namespace Slack\Tests;

use Dotenv\Dotenv;
use PHPUnit\Framework\TestCase;
use Slack\Api\Client;
use Slack\Tests\Commands\ChannelsHistoryTestCommand;

class ChannelsHistoryCommandSpec extends TestCase
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

    $this->command = new ChannelsHistoryTestCommand;
  }

  protected function tearDown ()
  {
    $env = null;
    $this->client = null;
    $this->command = null;
  }

  public function test_decoded_json_output ()
  {
    $decodedJson = json_decode($this->command->execute(["channel" => "C5BBK5MCP", "--output" => "json"]));

    $this->assertInternalType("object", $decodedJson);
    $this->assertInternalType("array", $decodedJson->messages);
    $this->assertInternalType("object", $decodedJson->messages[0]);
  }

  public function test_json_output ()
  {
    # NOTE: trim surrounding newline characters ('\n') for string assertions
    $jsonOutput = trim($this->command->execute(["channel" => "C5BBK5MCP", "--output" => "json"]), "\n ");

    $this->assertInternalType("string", $jsonOutput);

    $this->assertStringStartsWith("{", $jsonOutput);
    $this->assertStringEndsWith("}", $jsonOutput);

    $this->assertStringStartsNotWith("+", $jsonOutput);
    $this->assertStringEndsNotWith("+", $jsonOutput);
  }

  public function test_table_output ()
  {
    # NOTE: trim surrounding newline characters ('\n') for string assertions
    $tableOutput = trim($this->command->execute(["channel" => "C5BBK5MCP", "--output" => "table"]), "\n ");

    $this->assertInternalType("string", $tableOutput);

    $this->assertStringStartsWith("+", $tableOutput);
    $this->assertStringEndsWith("+", $tableOutput);

    $this->assertStringStartsNotWith("{", $tableOutput);
    $this->assertStringEndsNotWith("}", $tableOutput);
  }
}
