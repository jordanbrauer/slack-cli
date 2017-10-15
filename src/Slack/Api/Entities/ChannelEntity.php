<?php

declare (strict_types=1);

namespace Slack\Api\Entities;

/**
 * A channel entity contains information about a workspace channel.
 *
 * @author  Jordan Brauer <info@jordanbrauer.ca>
 * @version 0.0.1
 */
class ChannelEntity extends Entity
{
    /**
     * @var string $id The unique ID of the channel.
     */
    protected $id;

    /**
     * @var string $name The user friendly name of the channel.
     */
    protected $name;

    /**
     * @var bool $is_channel Is this object a Channel object?
     */
    protected $is_channel;

    /**
     * @var int $created The unix timestamp of when the channel was created.
     */
    protected $created;

    /**
     * @var string $creator The unique user ID of the user that created the channel.
     */
    protected $creator;

    /**
     * @var bool $is_archived Is the channel currently archived?
     */
    protected $is_archived;

    /**
     * @var bool $is_general Is the channel the default #general channel?
     */
    protected $is_general;

    /**
     * @var array $members A list of the unique user IDs of members within the channel.
     */
    protected $members;

    /**
     * @var array $topic An array containing the value, creator and last_set date of the channel topic.
     */
    protected $topic;

    /**
     * @var array $purpose An array containing the value, creator and last_set date of the channel purpose.
     */
    protected $purpose;

    /**
     * @var bool $is_member Will be true if the calling member is part of the channel.
     */
    protected $is_member;

    /**
     * @var string $last_read Is the timestamp for the last message the calling user has read in the channel.
     */
    protected $last_read;

    /**
     * @var array $latest Is the latest Message in the channel.
     */
    protected $latest;

    /**
     * @var int $unread_count Is a full count of visible messages that the calling user has yet to read.
     */
    protected $unread_count;

    /**
     * @var int $unread_count_display Is a count of messages that the calling user has yet to read that matter to them.
     */
    protected $unread_count_display;

    /**
     * @param string $id
     * @return $this
     */
    public function setId(string $id)
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param string $name
     * @return $this
     */
    public function setName(string $name)
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param bool $is_channel
     * @return $this
     */
    public function setIsChannel(bool $is_channel)
    {
        $this->is_channel = $is_channel;
        return $this;
    }

    /**
     * @return bool
     */
    public function getIsChannel()
    {
        return $this->is_channel;
    }

    /**
     * @param int $created
     * @return $this
     */
    public function setCreated(int $created)
    {
        $this->created = $created;
        return $this;
    }

    /**
     * @return int
     */
    public function getCreated()
    {
        return $this->created;
    }

    /**
     * @param string $creator
     * @return $this
     */
    public function setCreator(string $creator)
    {
        $this->creator = $creator;
        return $this;
    }

    /**
     * @return string
     */
    public function getCreator()
    {
        return $this->creator;
    }

    /**
     * @param bool $is_archived
     * @return $this
     */
    public function setIsArchived(bool $is_archived)
    {
        $this->is_archived = $is_archived;
        return $this;
    }

    /**
     * @return bool
     */
    public function getIsArchived()
    {
        return $this->is_archived;
    }

    /**
     * @param bool $is_general
     * @return $this
     */
    public function setIsGeneral(bool $is_general)
    {
        $this->is_general = $is_general;
        return $this;
    }

    /**
     * @return bool
     */
    public function getIsGeneral()
    {
        return $this->is_general;
    }

    /**
     * @param array $members
     * @return $this
     */
    public function setMembers(array $members)
    {
        $this->members = $members;
        return $this;
    }

    /**
     * @param int    $excluded
     * @param string $format
     * @return array|string
     */
    public function getMembers(int $excluded = 1, string $format = "string")
    {
        switch ($excluded):
            case 0:
                switch ($format) {
                    case "array":
                        return $this->members;
                        break;
                    case "string":
                        $members = "";
                        foreach ($this->members as $member):
                            $members .= "{$member},\n";
                        endforeach;

                        return $members;
                        break;
                    default:
                        return $this->members;
                }
                break;
            case 1:
                return "<fg=red>excluded</>";
                break;
        endswitch;
    }

    /**
     * @param array $topic
     * @return $this
     */
    public function setTopic(array $topic)
    {
        $this->topic = $topic;
        return $this;
    }

    /**
     * @param int    $length
     * @param string $format
     * @return array|mixed|string
     */
    public function getTopic(int $length = 14, string $format = "string")
    {
        switch ($format):
            case "array":
                return $this->topic;
                break;
            case "string":
                switch ($length) {
                    case -1:
                        return $this->topic["value"];
                        break;
                    default:
                        if (strlen($this->topic["value"]) < $length):
                            return $this->topic["value"];
                        else:
                            return substr($this->topic["value"], 0, $length) . " ...";
                        endif;
                }
                break;
            default:
                return $this->topic;
        endswitch;
    }

    /**
     * @param array $purpose
     * @return $this
     */
    public function setPurpose(array $purpose)
    {
        $this->purpose = $purpose;
        return $this;
    }

    /**
     * @param int    $length
     * @param string $format
     * @return array|mixed|string
     */
    public function getPurpose(int $length = 14, string $format = "string")
    {
        switch ($format):
            case "array":
                return $this->purpose;
                break;
            case "string":
                switch ($length) {
                    case -1:
                        return $this->purpose["value"];
                        break;
                    default:
                        if (strlen($this->purpose["value"]) < $length):
                            return $this->purpose["value"];
                        else:
                            return substr($this->purpose["value"], 0, $length) . " ...";
                        endif;
                }
                break;
            default:
                return $this->purpose;
        endswitch;
    }

    /**
     * @param bool $is_member
     * @return $this
     */
    public function setIsMember(bool $is_member)
    {
        $this->is_member = $is_member;
        return $this;
    }

    /**
     * @return bool
     */
    public function getIsMember()
    {
        return $this->is_member;
    }

    /**
     * @param string $last_read
     * @return $this
     */
    public function setLastRead(string $last_read)
    {
        $this->last_read = $last_read;
        return $this;
    }

    /**
     * @return string
     */
    public function getLastRead()
    {
        return $this->last_read;
    }

    /**
     * @param array $latest
     * @return $this
     */
    public function setLatest(array $latest)
    {
        $this->latest = $latest;
        return $this;
    }

    /**
     * @return array
     */
    public function getLatest()
    {
        return $this->latest;
    }

    /**
     * @param int $unread_count
     * @return $this
     */
    public function setUnreadCount(int $unread_count)
    {
        $this->unread_count = $unread_count;
        return $this;
    }

    /**
     * @return int
     */
    public function getUnreadCount()
    {
        return $this->unread_count;
    }

    /**
     * @param int $unread_count_display
     * @return $this
     */
    public function setUnreadCountDisplay(int $unread_count_display)
    {
        $this->unread_count_display = $unread_count_display;
        return $this;
    }

    /**
     * @return int
     */
    public function getUnreadCountDisplay()
    {
        return $this->unread_count_display;
    }
}
