<?php

namespace Slack\Tests;

use Dotenv\Dotenv;
use PHPUnit\Framework\TestCase;
use Slack\Api\Client;

class ApiClientOptionsSpec extends TestCase
{
  protected $client;

  protected function setUp ()
  {
    $env = new Dotenv(__DIR__."/../../");
    $env->load();

    $this->client = new Client([
      "base_url" => "https://slack.com/api",
      "token" => getenv("SLACK_API_TOKEN"),
    ]);
  }

  public function test_adding_options_to_query_string_parameters ()
  {
    $this->client->addOption("pretty", 1);
    $this->client->addOption("ugly", 0);
    $this->client->addOption("foo", "bar");

    $this->assertInternalType('array', $this->client->getOptions());
  }

  public function test_option_exists_check_validates_correctly ()
  {
    $this->client->addOption("foo", "bar");

    $this->assertTrue($this->client->optionExists("foo"));
    $this->assertFalse($this->client->optionExists("bar"));
  }
  
  public function test_query_string_parameters_are_valid ()
  {
    $this->client->addOption("pretty", 1);
    $this->client->addOption("ugly", 0);
    $this->client->addOption("foo", "bar");

    $this->assertInternalType('string', $this->client->getOptions("string"));
    $this->assertGreaterThan(0, strlen($this->client->getOptions("string")));
    $this->assertEquals(preg_match("/^\?([\w-]+(=[\w-]*)?(&[\w-]+(=[\w-]*)?)*)?$/", $this->client->getOptions("string")), 1);
  }
}
