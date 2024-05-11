<?php

// Generated by the protocol buffer compiler.  DO NOT EDIT!
// source: rpc.proto

namespace App\Grpc\nostr;

use Google\Protobuf\Internal\GPBUtil;

/**
 * Generated from protobuf message <code>RpcSendSignedEventResponse</code>
 */
class RpcSendSignedEventResponse extends \Google\Protobuf\Internal\Message
{
    /**
     * the group id of the event, will be used for gc, etc. If generated by a job it should be set to the job id
     *
     * Generated from protobuf field <code>string groupId = 1;</code>
     */
    protected $groupId = '';

    /**
     * Generated from protobuf field <code>bool success = 2;</code>
     */
    protected $success = false;

    /**
     * Constructor.
     *
     * @param  array  $data  {
     *                       Optional. Data for populating the Message object.
     *
     * @type string $groupId
     *              the group id of the event, will be used for gc, etc. If generated by a job it should be set to the job id
     * @type bool $success
     *            }
     */
    public function __construct($data = null)
    {
        \App\Grpc\nostr\GPBMetadata\Rpc::initOnce();
        parent::__construct($data);
    }

    /**
     * the group id of the event, will be used for gc, etc. If generated by a job it should be set to the job id
     *
     * Generated from protobuf field <code>string groupId = 1;</code>
     *
     * @return string
     */
    public function getGroupId()
    {
        return $this->groupId;
    }

    /**
     * the group id of the event, will be used for gc, etc. If generated by a job it should be set to the job id
     *
     * Generated from protobuf field <code>string groupId = 1;</code>
     *
     * @param  string  $var
     * @return $this
     */
    public function setGroupId($var)
    {
        GPBUtil::checkString($var, true);
        $this->groupId = $var;

        return $this;
    }

    /**
     * Generated from protobuf field <code>bool success = 2;</code>
     *
     * @return bool
     */
    public function getSuccess()
    {
        return $this->success;
    }

    /**
     * Generated from protobuf field <code>bool success = 2;</code>
     *
     * @param  bool  $var
     * @return $this
     */
    public function setSuccess($var)
    {
        GPBUtil::checkBool($var);
        $this->success = $var;

        return $this;
    }
}
