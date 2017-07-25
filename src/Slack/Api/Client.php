<?php

namespace Slack\Api;

use \GuzzleHttp\Client as Guzzle;
// use \GuzzleHttp\Exception\ClientException as GuzzleException; // TODO: #29

class Client extends Guzzle
{
  private $base_url;
  private $token;
  private $method;
  private $options;
  private $url;

  public function __construct (array $options = null)
  {
    parent::__construct();
    $this->options = array();
    $this->setBaseUrl($options["base_url"] ?? "https://slack.com/api");
    $this->setMethod($options["method"] ?? "auth.test");
    $this->setToken($options["token"]);
  }

  public function save ()
  {
    $this->url = "{$this->getBaseUrl()}/{$this->getMethod()}{$this->getQueryStrParams()}";
    return true;
  }

  public function load ()
  {
    return $this->url;
  }

  public function ping (string $method, array $options = null)
  {
    $this->setMethod($method);

    foreach ($options as $key => $value):
      $this->addOption($key, $value);
    endforeach;

    $this->save();

    return $this->post($this->load());
  }

  public function setBaseUrl (string $url, bool $ssl = true)
  {
    if (!filter_var($url, FILTER_VALIDATE_URL)):
      throw new \Exception("Error processing URL parameter for ".__FUNCTION__.", {$url}", 1);
    else:
      if (!array_key_exists("scheme", parse_url($url))):
        if ($ssl):
          $url = "https://{$url}";
        else:
          $url = "http://{$url}";
        endif;
      endif;

      $this->base_url = rtrim($url, "/");

      return $this;
    endif;
  }

  public function getBaseUrl ()
  {
    return $this->base_url;
  }

  public function setMethod (string $method)
  {
    $this->method = $method;
    return $this;
  }

  public function getMethod ()
  {
    return $this->method;
  }

  public function addOption (string $key, $value)
  {
    if (!$this->optionExists($key))
      $this->options[$key] = $value;

    return $this;
  }

  public function optionExists (string $key)
  {
    if (array_key_exists($key, $this->getOptions()))
      return true;

    return false;
  }

  public function getOption (string $key)
  {
    return $this->options[$key];
  }

  public function getOptions (string $format = "array")
  {
    if ($format == "array")
      return $this->options;

    if ($format == "string")
      return "?".http_build_query($this->options);
  }

  public function setToken (string $token)
  {
    $this->token = $token;
    $this->addOption("token", $token);

    return $this;
  }

  public function getToken ()
  {
    return $this->token;
  }

  public function getQueryStrParams ()
  {
    return $this->getOptions("string");
  }
}
