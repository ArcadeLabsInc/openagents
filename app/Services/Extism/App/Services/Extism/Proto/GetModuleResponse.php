<?php
# Generated by the protocol buffer compiler.  DO NOT EDIT!
# source: api.proto

namespace App\Services\Extism\Proto;

use Google\Protobuf\Internal\GPBType;
use Google\Protobuf\Internal\RepeatedField;
use Google\Protobuf\Internal\GPBUtil;

/**
 * The message returned in response to a `GetModuleRequest`.
 *
 * Generated from protobuf message <code>GetModuleResponse</code>
 */
class GetModuleResponse extends \Google\Protobuf\Internal\Message
{
    /**
     * Generated from protobuf field <code>.Module module = 1;</code>
     */
    protected $module = null;
    /**
     * Generated from protobuf field <code>optional .Error error = 2;</code>
     */
    protected $error = null;

    /**
     * Constructor.
     *
     * @param array $data {
     *     Optional. Data for populating the Message object.
     *
     *     @type \App\Services\Extism\Proto\Module $module
     *     @type \App\Services\Extism\Proto\Error $error
     * }
     */
    public function __construct($data = NULL) {
        \GPBMetadata\Api::initOnce();
        parent::__construct($data);
    }

    /**
     * Generated from protobuf field <code>.Module module = 1;</code>
     * @return \App\Services\Extism\Proto\Module|null
     */
    public function getModule()
    {
        return $this->module;
    }

    public function hasModule()
    {
        return isset($this->module);
    }

    public function clearModule()
    {
        unset($this->module);
    }

    /**
     * Generated from protobuf field <code>.Module module = 1;</code>
     * @param \App\Services\Extism\Proto\Module $var
     * @return $this
     */
    public function setModule($var)
    {
        GPBUtil::checkMessage($var, \App\Services\Extism\Proto\Module::class);
        $this->module = $var;

        return $this;
    }

    /**
     * Generated from protobuf field <code>optional .Error error = 2;</code>
     * @return \App\Services\Extism\Proto\Error|null
     */
    public function getError()
    {
        return $this->error;
    }

    public function hasError()
    {
        return isset($this->error);
    }

    public function clearError()
    {
        unset($this->error);
    }

    /**
     * Generated from protobuf field <code>optional .Error error = 2;</code>
     * @param \App\Services\Extism\Proto\Error $var
     * @return $this
     */
    public function setError($var)
    {
        GPBUtil::checkMessage($var, \App\Services\Extism\Proto\Error::class);
        $this->error = $var;

        return $this;
    }

}

