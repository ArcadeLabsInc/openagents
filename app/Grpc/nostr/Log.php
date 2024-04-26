<?php
# Generated by the protocol buffer compiler.  DO NOT EDIT!
# source: Log.proto

namespace App\Grpc\nostr;

use Google\Protobuf\Internal\GPBType;
use Google\Protobuf\Internal\RepeatedField;
use Google\Protobuf\Internal\GPBUtil;

/**
 * Generated from protobuf message <code>Log</code>
 */
class Log extends \Google\Protobuf\Internal\Message
{
    /**
     * Generated from protobuf field <code>string id = 1;</code>
     */
    protected $id = '';
    /**
     * Generated from protobuf field <code>string log = 2;</code>
     */
    protected $log = '';
    /**
     * Generated from protobuf field <code>string level = 3;</code>
     */
    protected $level = '';
    /**
     * Generated from protobuf field <code>uint64 timestamp = 4;</code>
     */
    protected $timestamp = 0;
    /**
     * Generated from protobuf field <code>string source = 5;</code>
     */
    protected $source = '';
    /**
     * Generated from protobuf field <code>string nodeId = 6;</code>
     */
    protected $nodeId = '';

    /**
     * Constructor.
     *
     * @param array $data {
     *     Optional. Data for populating the Message object.
     *
     *     @type string $id
     *     @type string $log
     *     @type string $level
     *     @type int|string $timestamp
     *     @type string $source
     *     @type string $nodeId
     * }
     */
    public function __construct($data = NULL) {
        \App\Grpc\nostr\GPBMetadata\Log::initOnce();
        parent::__construct($data);
    }

    /**
     * Generated from protobuf field <code>string id = 1;</code>
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Generated from protobuf field <code>string id = 1;</code>
     * @param string $var
     * @return $this
     */
    public function setId($var)
    {
        GPBUtil::checkString($var, True);
        $this->id = $var;

        return $this;
    }

    /**
     * Generated from protobuf field <code>string log = 2;</code>
     * @return string
     */
    public function getLog()
    {
        return $this->log;
    }

    /**
     * Generated from protobuf field <code>string log = 2;</code>
     * @param string $var
     * @return $this
     */
    public function setLog($var)
    {
        GPBUtil::checkString($var, True);
        $this->log = $var;

        return $this;
    }

    /**
     * Generated from protobuf field <code>string level = 3;</code>
     * @return string
     */
    public function getLevel()
    {
        return $this->level;
    }

    /**
     * Generated from protobuf field <code>string level = 3;</code>
     * @param string $var
     * @return $this
     */
    public function setLevel($var)
    {
        GPBUtil::checkString($var, True);
        $this->level = $var;

        return $this;
    }

    /**
     * Generated from protobuf field <code>uint64 timestamp = 4;</code>
     * @return int|string
     */
    public function getTimestamp()
    {
        return $this->timestamp;
    }

    /**
     * Generated from protobuf field <code>uint64 timestamp = 4;</code>
     * @param int|string $var
     * @return $this
     */
    public function setTimestamp($var)
    {
        GPBUtil::checkUint64($var);
        $this->timestamp = $var;

        return $this;
    }

    /**
     * Generated from protobuf field <code>string source = 5;</code>
     * @return string
     */
    public function getSource()
    {
        return $this->source;
    }

    /**
     * Generated from protobuf field <code>string source = 5;</code>
     * @param string $var
     * @return $this
     */
    public function setSource($var)
    {
        GPBUtil::checkString($var, True);
        $this->source = $var;

        return $this;
    }

    /**
     * Generated from protobuf field <code>string nodeId = 6;</code>
     * @return string
     */
    public function getNodeId()
    {
        return $this->nodeId;
    }

    /**
     * Generated from protobuf field <code>string nodeId = 6;</code>
     * @param string $var
     * @return $this
     */
    public function setNodeId($var)
    {
        GPBUtil::checkString($var, True);
        $this->nodeId = $var;

        return $this;
    }

}

