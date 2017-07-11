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

  public function test_client_authentication ()
  {
    $request = $this->client->ping("auth.test", ["pretty" => 1]);
    $response = $request->getStatusCode();

    $this->assertEquals($response, 200);
  }
}
