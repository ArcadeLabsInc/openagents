<?php

// Generated by the protocol buffer compiler.  DO NOT EDIT!
// source: rpc.proto

namespace App\Grpc\nostr;

use Google\Protobuf\Internal\GPBUtil;

/**
 * Generated from protobuf message <code>RpcGetJob</code>
 */
class RpcGetJob extends \Google\Protobuf\Internal\Message
{
    /**
     * Generated from protobuf field <code>string jobId = 1;</code>
     */
    protected $jobId = '';

    /**
     * max time to wait in ms , 0 or unset means no wait
     *
     * Generated from protobuf field <code>optional uint32 wait = 99;</code>
     */
    protected $wait = null;

    /**
     * Constructor.
     *
     * @param  array  $data  {
     *                       Optional. Data for populating the Message object.
     *
     * @type string $jobId
     * @type int $wait
     *           max time to wait in ms , 0 or unset means no wait
     *           }
     */
    public function __construct($data = null)
    {
        \App\Grpc\nostr\GPBMetadata\Rpc::initOnce();
        parent::__construct($data);
    }

    /**
     * Generated from protobuf field <code>string jobId = 1;</code>
     *
     * @return string
     */
    public function getJobId()
    {
        return $this->jobId;
    }

    /**
     * Generated from protobuf field <code>string jobId = 1;</code>
     *
     * @param  string  $var
     * @return $this
     */
    public function setJobId($var)
    {
        GPBUtil::checkString($var, true);
        $this->jobId = $var;

        return $this;
    }

    /**
     * max time to wait in ms , 0 or unset means no wait
     *
     * Generated from protobuf field <code>optional uint32 wait = 99;</code>
     *
     * @return int
     */
    public function getWait()
    {
        return isset($this->wait) ? $this->wait : 0;
    }

    public function hasWait()
    {
        return isset($this->wait);
    }

    public function clearWait()
    {
        unset($this->wait);
    }

    /**
     * max time to wait in ms , 0 or unset means no wait
     *
     * Generated from protobuf field <code>optional uint32 wait = 99;</code>
     *
     * @param  int  $var
     * @return $this
     */
    public function setWait($var)
    {
        GPBUtil::checkUint32($var);
        $this->wait = $var;

        return $this;
    }
}
