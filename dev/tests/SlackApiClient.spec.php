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

  public function test_client_base_url ()
  {
    $this->client->setBaseUrl("https://slack.com/api/");

    $this->assertInternalType('string', $this->client->getBaseUrl());
    $this->assertTrue(mb_substr($this->client->getBaseUrl(), -1) != "/");
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

  public function test_constructed_url_is_ready_for_api_request ()
  {
    $url = "https://slack.com/api/auth.test?token=".getenv("SLACK_API_TOKEN")."&pretty=1";

    $this->client
      ->addOption("pretty", 1)
      ->save(true);

    $this->assertEquals($url, $this->client->load());
  }

  public function test_client_authentication ()
  {
    $request = $this->client->ping("auth.test", ["pretty" => 1]);
    $response = $request->getStatusCode();

    $this->assertEquals($response, 200);
  }
}
