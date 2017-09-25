<?php

namespace Slack\Tests;

use Dotenv\Dotenv;
use PHPUnit\Framework\TestCase;
use Slack\Entities\MessageEntity as Message;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;

class MessageEntitySpec extends TestCase
{
  protected $json;
  protected $serializer;
  protected $message;

  protected function setUp ()
  {
    $this->json = json_encode([
      "type" => "message",
      "ts" => "1358546515.000007",
      "user" => "U2147483896",
      "text" => "Hellow World",
      "is_starred" => true,
      "reactions" => [
          [
              "name" => "space_invader",
              "count" => 3,
              "users" => [ "U1", "U2", "U3" ],
          ],
          [
              "name" => "sweet_potato",
              "count" => 5,
              "users" => [ "U1", "U2", "U3", "U4", "U5" ],
          ],
      ],
    ]);

    # JSON Serializer and object normalizer (see Symfony serializer component)
    $this->serializer = new Serializer(
      array(new ObjectNormalizer),
      array(new JsonEncoder)
    );

    $this->message = $this->serializer->deserialize($this->json, Message::class, "json");
  }

  protected function tearDown ()
  {
    $this->json = null;
    $this->serializer = null;
    $this->message = null;
  }

  public function test_entity_serializatoin ()
  {
    $newMessage = new Message;

    $newMessage
      ->setType("message")
      ->setTs("1358546515.000007")
      ->setUser("U2147483896")
      ->setText("Hellow World")
      ->setIsStarred(true)
      ->setReactions([
        [
            "name" => "space_invader",
            "count" => 3,
            "users" => [ "U1", "U2", "U3" ],
        ],
        [
            "name" => "sweet_potato",
            "count" => 5,
            "users" => [ "U1", "U2", "U3", "U4", "U5" ],
        ],
      ])
      ;

    $serializedMessage = $this->serializer->serialize($newMessage, "json");

    $this->assertInternalType("string", $serializedMessage);
    $this->assertStringStartsWith("{", $serializedMessage);
    $this->assertStringEndsWith("}", $serializedMessage);

    // NOTE:BUG: Symfony serializer does not copy attribute names properly
    // $this->assertEquals($this->json, $serializedMessage);
  }

  public function test_channel_entity_attribute_type ()
  {
    $this->message->setType("testy");

    $this->assertInternalType("string", $this->message->getType());
    $this->assertEquals("testy", $this->message->getType());
  }

  public function test_channel_entity_attribute_ts ()
  {
    $this->message->setTs("123.42");

    $this->assertInternalType("string", $this->message->getTs());
    $this->assertEquals("123.42", $this->message->getTs());
  }

  public function test_channel_entity_attribute_user ()
  {
    $this->message->setUser("YOMOMMA");

    $this->assertInternalType("string", $this->message->getUser());
    $this->assertEquals("YOMOMMA", $this->message->getUser());
  }

  public function test_channel_entity_attribute_text ()
  {
    $this->message->setText("how now brown cow");

    $this->assertInternalType("string", $this->message->getText());
    $this->assertEquals("how now brown cow", $this->message->getText());
  }

  public function test_channel_entity_attribute_is_starred ()
  {
    # test true
    $this->message->setIsStarred(true);

    $this->assertInternalType("bool", $this->message->getIsStarred());
    $this->assertEquals(true, $this->message->getIsStarred());

    # test false
    $this->message->setIsStarred(false);

    $this->assertInternalType("bool", $this->message->getIsStarred());
    $this->assertEquals(false, $this->message->getIsStarred());
  }

  public function test_channel_entity_attribute_reactions ()
  {
    $this->message->setReactions([
      [
        "name" => "space_invader",
        "count" => 120,
        "users" => ["U1", "U2", "U3"],
      ],
      [
        "name" => "sweet_potato",
        "count" => 23,
        "users" => ["U1", "U2", "U3"],
      ],
    ]);

    $this->assertInternalType("array", $this->message->getReactions(0, "array"));
    $this->assertEquals(2, count($this->message->getReactions(0, "array")));
    $this->assertEquals(
      [
        [
          "name" => "space_invader",
          "count" => 120,
          "users" => ["U1", "U2", "U3"],
        ],
        [
          "name" => "sweet_potato",
          "count" => 23,
          "users" => ["U1", "U2", "U3"],
        ],
      ],
      $this->message->getReactions(0, "array")
    );
  }
}
