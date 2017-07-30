<?php

namespace Slack\Entities;

class Channel
{
  private $id; // string
  private $name; // string
  private $is_channel; // bool
  private $created; // int
  private $creator; // string
  private $is_archived; // bool
  private $is_general; // bool
  private $members; // array
  private $topic; // array (object) - value, creator, last_set
  private $purpose; // array (object) - value, creator, last_set
  private $is_member; // bool
  private $last_read; // string
  private $latest; // array
  private $unread_count; // int
  private $unread_count_display; // int

  public function setId (string $id)
  {
    $this->id = $id;
    return $this;
  }

  public function getId ()
  {
    return $this->id;
  }
}
