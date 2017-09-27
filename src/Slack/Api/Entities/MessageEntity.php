<?php

/**
 * @author Jordan Brauer <info@jordanbrauer.ca>
 * @version 0.0.1
 */

declare (strict_types = 1);

namespace Slack\Api\Entities;

class MessageEntity extends Entity
{
  /**
   * @var string $type The type of message. */
  protected $type;

  /**
   * @var string $type The subtype of message. E.g., channel joins, events, etc */
  protected $subtype;

  /**
   * @var string $channel The channel that the message belongs to. */
  protected $channel;

  /**
   * @var string $user The unique user ID of the user that sent the message. */
  protected $user;

  /**
   * @var string $text The messages text */
  protected $text;

  /**
   * @var string $ts The message timestamp */
  protected $ts;

  /**
   * @var bool $is_starred The is_starred property is present and true if the calling user has starred the message, else it is omitted. */
  protected $is_starred;

  /**
   * @var array $pinned_to If present, contains the IDs of any channels to which the message is currently pinned. */
  protected $pinned_to;

  /**
   * @var array $reactions An array of reactions, their counts, and reacting users. See section "## Stars, pins, and reactions" for more info.
   * @link https://api.slack.com/events/message
   */
  protected $reactions;

  public function setType (string $type)
  {
    $this->type = $type;
    return $this;
  }

  public function getType ()
  {
    return $this->type;
  }

  public function setSubtype (string $subtype)
  {
    $this->subtype = $subtype;
    return $this;
  }

  public function getSubtype ()
  {
    return $this->subtype;
  }

  public function setChannel (string $channel)
  {
    $this->channel = $channel;
    return $this;
  }

  public function getChannel ()
  {
    return $this->channel;
  }

  public function setUser (string $user)
  {
    $this->user = $user;
    return $this;
  }

  public function getUser ()
  {
    return $this->user;
  }

  public function setText (string $text)
  {
    $this->text = $text;
    return $this;
  }

  public function getText ()
  {
    return $this->text;
  }

  public function setTs (string $ts)
  {
    $this->ts = $ts;
    return $this;
  }

  public function getTs ()
  {
    return $this->ts;
  }

  public function setIsStarred (bool $is_starred)
  {
    $this->is_starred = $is_starred;
    return $this;
  }

  public function getIsStarred ()
  {
    return $this->is_starred;
  }

  public function setPinnedTo (array $pinned_to)
  {
    $this->pinned_to = $pinned_to;
    return $this;
  }

  public function getPinnedTo
  (int $excluded = 1, string $format = "string")
  {
    switch ($excluded):
      case 0:
        switch ($format) {
          case "array":
            return $this->pinned_to;
            break;
          case "string":
            $pinned_to = "";
            foreach ($this->pinned_to as $channel):
              $pinned_to .= "{$channel},\n";
            endforeach;

            return $pinned_to;
            break;
        }
        break;
      case 1:
        return "<fg=red>excluded</>";
        break;
    endswitch;
  }

  public function setReactions (array $reactions)
  {
    $this->reactions = $reactions;
    return $this;
  }

  public function getReactions
  (int $excluded = 1, string $format = "string")
  {
    switch ($excluded):
      case 0:
        switch ($format) {
          case "array":
            return $this->reactions;
            break;
          case "string":
            $reactions = "";
            foreach ($this->reactions as $attribute => $value):
              $reactions .= "\"{$attribute}\": \"{$value}\",\n";
            endforeach;

            return $reactions;
            break;
        }
        break;
      case 1:
        return "<fg=red>excluded</>";
        break;
    endswitch;
  }
}
