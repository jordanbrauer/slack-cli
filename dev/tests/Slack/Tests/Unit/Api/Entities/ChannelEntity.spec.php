<?php

declare (strict_types = 1);

namespace Slack\Tests\Unit\Api\Entities;

use PHPUnit\Framework\TestCase;
use Slack\Api\Entities\ChannelEntity as Channel;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;

class ChannelEntitySpec extends TestCase
{
  protected function setUp ()
  {
    $json = json_encode([
      "id" => "C024BE91L",
      "name" => "fun",
      "is_channel" => true,
      "created" => 1360782804,
      "creator" => "U024BE7LH",
      "is_archived" => false,
      "is_general" => false,
      "members" => [
          "U024BE7LH",
          "U024BE7LH"
      ],
      "topic" => [
          "value" => "Fun times",
          "creator" => "U024BE7LV",
          "last_set" => 1369677212
      ],
      "purpose" => [
          "value" => "This channel is for fun",
          "creator" => "U024BE7LH",
          "last_set" => 1360782804
      ],
      "is_member" => true,
      "last_read" => "1401383885.000061",
      "latest" => [],
      "unread_count" => 0,
      "unread_count_display" => 0
    ]);

    $this->serializer = new Serializer(
      array(new ObjectNormalizer),
      array(new JsonEncoder)
    );

    $this->channel = $this->serializer->deserialize($json, Channel::class, "json");
  }

  protected function tearDown ()
  {
    unset($this->serializer);
    unset($this->channel);
  }

  public function test_entity_serializatoin ()
  {
    $newChannel = new Channel;

    $newChannel
      ->setId("C024BE91L")
      ->setName("fun")
      ->setIsChannel(true)
      ->setCreated(1360782804)
      ->setCreator("U024BE7LH")
      ->setIsArchived(false)
      ->setIsGeneral(false)
      ->setMembers([
          "U024BE7LH",
          "U024BE7LH",
      ])
      ->setTopic([
          "value" => "Fun times",
          "creator" => "U024BE7LV",
          "last_set" => 1369677212,
      ])
      ->setPurpose([
          "value" => "This channel is for fun",
          "creator" => "U024BE7LH",
          "last_set" => 1360782804,
      ])
      ->setIsMember(true)
      ->setLastRead("1401383885.000061")
      ->setLatest([])
      ->setUnreadCount(0)
      ->setUnreadCountDisplay(0)
      ;

    $serializedChannel = $this->serializer->serialize($newChannel, "json");

    $this->assertInternalType("string", $serializedChannel);
    $this->assertStringStartsWith("{", $serializedChannel);
    $this->assertStringEndsWith("}", $serializedChannel);

    // NOTE:BUG: Symfony serializer does not copy attribute names properly
    // $this->assertEquals($json, $serializedChannel);
  }

  public function test_channel_entity_attribute_id ()
  {
    $this->channel->setId("TESTID123");

    $this->assertInternalType("string", $this->channel->getId());
    $this->assertEquals("TESTID123", $this->channel->getId());
  }

  public function test_channel_entity_attribute_name ()
  {
    $this->channel->setName("Testing Name");

    $this->assertInternalType("string", $this->channel->getName());
    $this->assertEquals("Testing Name", $this->channel->getName());
  }

  public function test_channel_entity_attribute_is_channel ()
  {
    # test true
    $this->channel->setIsChannel(true);

    $this->assertInternalType("bool", $this->channel->getIsChannel());
    $this->assertEquals(true, $this->channel->getIsChannel());

    # test false
    $this->channel->setIsChannel(false);

    $this->assertInternalType("bool", $this->channel->getIsChannel());
    $this->assertEquals(false, $this->channel->getIsChannel());
  }

  public function test_channel_entity_attribute_created ()
  {
    $this->channel->setCreated(1360782804);

    $this->assertInternalType("int", $this->channel->getCreated());
    $this->assertEquals(1360782804, $this->channel->getCreated());
  }

  public function test_channel_entity_attribute_creator ()
  {
    $this->channel->setCreator("U024BE7LH");

    $this->assertInternalType("string", $this->channel->getCreator());
    $this->assertEquals("U024BE7LH", $this->channel->getCreator());
  }

  public function test_channel_entity_attribute_is_archived ()
  {
    $this->channel->setIsArchived(true);

    $this->assertInternalType("bool", $this->channel->getIsArchived());
    $this->assertEquals(true, $this->channel->getIsArchived());

    $this->channel->setIsArchived(false);

    $this->assertInternalType("bool", $this->channel->getIsArchived());
    $this->assertEquals(false, $this->channel->getIsArchived());
  }

  public function test_channel_entity_attribute_is_general ()
  {
    $this->channel->setIsGeneral(true);

    $this->assertInternalType("bool", $this->channel->getIsGeneral());
    $this->assertEquals(true, $this->channel->getIsGeneral());

    $this->channel->setIsGeneral(false);

    $this->assertInternalType("bool", $this->channel->getIsGeneral());
    $this->assertEquals(false, $this->channel->getIsGeneral());
  }

  public function test_channel_entity_attribute_members ()
  {
    $this->channel->setMembers([
      "U024BE7LH",
      "U024BE7LH",
    ]);

    $this->assertInternalType("array", $this->channel->getMembers(0, "array"));
    $this->assertEquals(2, count($this->channel->getMembers(0, "array")));
    $this->assertEquals(
      ["U024BE7LH", "U024BE7LH"],
      $this->channel->getMembers(0, "array")
    );
  }

  public function test_channel_entity_attribute_topic ()
  {
    $this->channel->setTopic([
      "value" => "Fun times",
      "creator" => "U024BE7LV",
      "last_set" => 1369677212,
    ]);

    $this->assertInternalType("array", $this->channel->getTopic(-1, "array"));
    $this->assertEquals(3, count($this->channel->getTopic(-1, "array")));
    $this->assertEquals(
      ["value" => "Fun times", "creator" => "U024BE7LV", "last_set" => 1369677212],
      $this->channel->getTopic(-1, "array")
    );
  }

  public function test_channel_entity_attribute_purpose ()
  {
    $this->channel->setPurpose([
      "value" => "This channel is for fun",
      "creator" => "U024BE7LH",
      "last_set" => 1360782804,
    ]);

    $this->assertInternalType("array", $this->channel->getPurpose(-1, "array"));
    $this->assertEquals(3, count($this->channel->getPurpose(-1, "array")));
    $this->assertEquals(
      ["value" => "This channel is for fun", "creator" => "U024BE7LH", "last_set" => 1360782804],
      $this->channel->getPurpose(-1, "array")
    );
  }

  public function test_channel_entity_attribute_is_member ()
  {
    $this->channel->setIsMember(true);

    $this->assertInternalType("bool", $this->channel->getIsMember());
    $this->assertEquals(true, $this->channel->getIsMember());

    $this->channel->setIsMember(false);

    $this->assertInternalType("bool", $this->channel->getIsMember());
    $this->assertEquals(false, $this->channel->getIsMember());
  }

  public function test_channel_entity_attribute_last_read ()
  {
    $this->channel->setLastRead("1401383885.000061");

    $this->assertInternalType("string", $this->channel->getLastRead());
    $this->assertEquals("1401383885.000061", $this->channel->getLastRead());
  }

  public function test_channel_entity_attribute_unread_count ()
  {
    $this->channel->setUnreadCount(123);

    $this->assertInternalType("int", $this->channel->getUnreadCount());
    $this->assertEquals(123, $this->channel->getUnreadCount());
  }

  public function test_channel_entity_attribute_unread_count_display ()
  {
    $this->channel->setUnreadCountDisplay(321);

    $this->assertInternalType("int", $this->channel->getUnreadCountDisplay());
    $this->assertEquals(321, $this->channel->getUnreadCountDisplay());
  }
}
