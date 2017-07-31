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
    $env = new Dotenv(__DIR__."/../../../");
    $env->load();

    $this->client = new Client();
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

    $this->client->setBaseUrl("http://google.ca/");

    $this->assertStringStartsWith("http://", $this->client->getBaseUrl());
    $this->assertStringEndsNotWith("/", $this->client->getBaseUrl());

    $this->client->setBaseUrl("https://google.ca/");

    $this->assertStringStartsWith("https://", $this->client->getBaseUrl());
    $this->assertStringEndsNotWith("/", $this->client->getBaseUrl());
  }

  public function test_get_base_url_method ()
  {
    $this->client->setBaseUrl("google.com");

    $this->assertInternalType("string", $this->client->getBaseUrl());
    $this->assertNotFalse(filter_var($this->client->getBaseUrl(), FILTER_VALIDATE_URL));
  }

  public function test_add_option_method ()
  {
    $this->assertArrayNotHasKey("opshuns", $this->client->getParams());

    $this->client->addParam("opshuns", "test");

    $this->assertArrayHasKey("opshuns", $this->client->getParams());

    $this->client->addParam("pretty", 1);
    $this->client->addParam("ugly", 0);
    $this->client->addParam("foo", "bar");

    $this->assertInternalType("array", $this->client->getParams());
    $this->assertInternalType("string", $this->client->getParams("string"));
  }

  public function test_option_exists_method ()
  {
    $this->client->addParam("foo", "bar");

    $this->assertTrue($this->client->paramExists("foo"));
    $this->assertFalse($this->client->paramExists("bar"));
  }

  public function test_query_string_parameters_are_valid ()
  {
    $this->client->addParam("pretty", 1);
    $this->client->addParam("ugly", 0);
    $this->client->addParam("foo", "bar");

    $this->assertInternalType("string", $this->client->getParams("string"));
    $this->assertGreaterThan(0, strlen($this->client->getParams("string")));
    $this->assertEquals(preg_match("/^\?([\w-]+(=[\w-]*)?(&[\w-]+(=[\w-]*)?)*)?$/", $this->client->getParams("string")), 1);
  }

  public function test_client_base_url ()
  {
    $this->client->setBaseUrl("https://slack.com/api/");

    $this->assertInternalType("string", $this->client->getBaseUrl());
    $this->assertTrue(mb_substr($this->client->getBaseUrl(), -1) != "/");
  }

  public function test_constructed_url_is_ready_for_api_request ()
  {
    $url = "https://slack.com/api/auth.test?token=".getenv("SLACK_API_TOKEN")."&pretty=1";

    $this->client
      ->setBaseUrl("https://slack.com/api/")
      ->setToken(getenv("SLACK_API_TOKEN"))
      ->setMethod("auth.test")
      ->addParam("pretty", 1)
      ;

    $this->assertEquals($url, $this->client->load());
  }

  public function test_client_authentication ()
  {
    $this->client
      ->setBaseUrl("https://slack.com/api/")
      ->setToken(getenv("SLACK_API_TOKEN"))
      ;

    $request = $this->client->ping("auth.test", ["pretty" => 1]);
    $response = $request->getStatusCode();

    $this->assertEquals($response, 200);
  }
}
