<?php

namespace Slack\Tests;

use Dotenv\Dotenv;
use PHPUnit\Framework\TestCase;
use Slack\Api\Client;

class ApiClientSpec extends TestCase
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

  protected function tearDown ()
  {
    $env = null;
    $this->client = null;
  }

  public function test_set_base_url_method ()
  {
    $this->client->setBaseUrl("google.ca/");

    $this->assertStringStartsWith("https://", $this->client->getBaseUrl());
    $this->assertStringEndsNotWith("/", $this->client->getBaseUrl());

    $this->client->setBaseUrl("google.ca/", false);

    $this->assertStringStartsWith("http://", $this->client->getBaseUrl());
    $this->assertStringEndsNotWith("/", $this->client->getBaseUrl());
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

  public function test_client_base_url ()
  {
    $this->client->setBaseUrl("https://slack.com/api/");

    $this->assertInternalType('string', $this->client->getBaseUrl());
    $this->assertTrue(mb_substr($this->client->getBaseUrl(), -1) != "/");
  }

  public function test_constructed_url_is_ready_for_api_request ()
  {
    $url = "https://slack.com/api/auth.test?token=".getenv("SLACK_API_TOKEN")."&pretty=1";

    $this->client
      ->addOption("pretty", 1)
      ->save();

    $this->assertEquals($url, $this->client->load());
  }

  public function test_client_authentication ()
  {
    $request = $this->client->ping("auth.test", ["pretty" => 1]);
    $response = $request->getStatusCode();

    $this->assertEquals($response, 200);
  }
}
