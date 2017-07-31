<?php

/**
 * @author Jordan Brauer <info@jordanbrauer.ca>
 * @version 1.0.0
 * @license MIT
 */

namespace Slack\Api;

use \GuzzleHttp\Client as Guzzle;
// use \GuzzleHttp\Exception\ClientException as GuzzleException; // TODO: #29

class Client extends Guzzle
{
  /**
   * @var string $base_url The base URL of the Slack API. */
  private $base_url;

  /**
   * @var string $token Your Slack teams API token. */
  private $token;

  /**
   * @var string $method The Slack API method that is being executed. */
  private $method;

  /**
   * @var array $params An array of URL paramters being passed to the current method. */
  private $params;

  /**
   * @var string $url The complete URL to be pinged with a POST request. */
  private $url;

  /**
   * The Client object contructor.
   * @method __construct
   * @param array $options An array containg the required base parameters for API usage.
   */
  public function __construct (array $options = null)
  {
    parent::__construct();

    $this->params = array();

    if ($options):
      if (array_key_exists("base_url", $options))
      $this->setBaseUrl($options["base_url"]);

      if (array_key_exists("token", $options))
      $this->setToken($options["token"]);
    endif;
  }

  /**
   * Set the complete URL with the current configured parameters.
   * @method save
   * @return true
   */
  public function save ()
  {
    $this->url = "{$this->getBaseUrl()}/{$this->getMethod()}{$this->getQueryStrParams()}";
    return true;
  }

  /**
   * Returns the current complete URL.
   * @method load
   * @return string
   */
  public function load ()
  {
    return $this->url;
  }

  /**
   * Hit a Slack API method with a POST request.
   * @method ping
   * @param string $method The Slack API method being hit with a POST request.
   * @param array $options An array of options being sent as arguments with the method.
   * @return object
   */
  public function ping
  (string $method, array $options = null)
  {
    $this->setMethod($method);

    if ($options):
      foreach ($options as $key => $value):
        $this->addParam($key, $value);
      endforeach;
    endif;

    $this->save();

    return $this->post($this->load());
  }

  /**
   * Set the base URL for the Client.
   * @method setBaseUrl
   * @param string $url The base url being built upon to form a complete URL for a request.
   * @param bool $forceSSL Force (or don't) HTTPS protocol.
   */
  public function setBaseUrl (string $url, bool $forceSSL = true)
  {
    $protocol = $forceSSL ?
      "https://" : "http://";

    if (!array_key_exists("scheme", parse_url($url))):
      $url = "{$protocol}{$url}";
    endif;

    $this->base_url = rtrim($url, "/");

    $this->save();

    return $this;
  }

  /**
   * Retreive the base URL of the Client
   * @method getBaseUrl
   * @return string
   */
  public function getBaseUrl ()
  {
    return $this->base_url;
  }

  /**
   * Set the API method being called.
   * @method setMethod
   * @param string $method The Slack API method being called - see https://api.slack.com/methods
   */
  public function setMethod (string $method)
  {
    $this->method = $method;
    $this->save();
    return $this;
  }

  /**
   * Retreive the current method being called.
   * @method getMethod
   * @return string
   */
  public function getMethod ()
  {
    return $this->method;
  }

  /**
   * Set the API token for the Client
   * @method setToken
   * @param string $token Your Slack team' API token
   */
  public function setToken (string $token)
  {
    $this->token = $token;
    $this->addParam("token", $token);

    return $this;
  }

  /**
   * Retreive the current Client token.
   * @return string
   */
  public function getToken ()
  {
    return $this->token;
  }

  /**
   * Add a URL paramter to the method being called
   * @method addParam
   * @param string $key The name of the parameter being passed to the current method
   * @param mixed $value The value of the parameter. **String and Int types only**.
   */
  public function addParam (string $key, $value)
  {
    if (!$this->paramExists($key))
      $this->params[$key] = $value;

    $this->save();

    return $this;
  }

  /**
   * Check if a parameter exists on the current Client
   * @method paramExists
   * @param string $key The name of the parameter being checked for.
   * @return bool
   */
  public function paramExists (string $key)
  {
    if (array_key_exists($key, $this->getParams()))
      return true;

    return false;
  }

  /**
   * Retreive a defined parameters' value
   * @method getParam
   * @param string $key The name of the paramter being retreived.
   * @return mixed
   */
  public function getParam (string $key)
  {
    return $this->params[$key];
  }

  /**
   * Retreive the full set of current Client parameters
   * @method getParams
   * @param string $format A scalar type to have the parameters returned as. Possible values include `array` and `string`.
   * @return mixed
   */
  public function getParams (string $format = "array")
  {
    if ($format == "array")
      return $this->params;

    if ($format == "string")
      return "?".http_build_query($this->params);
  }

  /**
   * Get the full set of current Client paramaters as a query string.
   * @method getQueryStrParams
   * @return string
   */
  public function getQueryStrParams ()
  {
    return $this->getParams("string");
  }
}
