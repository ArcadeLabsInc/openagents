<?php

// Generated by the protocol buffer compiler.  DO NOT EDIT!
// source: Job.proto

namespace App\Grpc\nostr\GPBMetadata;

class Job
{
    public static $is_initialized = false;

    public static function initOnce()
    {
        $pool = \Google\Protobuf\Internal\DescriptorPool::getGeneratedPool();

        if (static::$is_initialized == true) {
            return;
        }
        \App\Grpc\nostr\GPBMetadata\JobParam::initOnce();
        \App\Grpc\nostr\GPBMetadata\JobInput::initOnce();
        \App\Grpc\nostr\GPBMetadata\JobState::initOnce();
        \App\Grpc\nostr\GPBMetadata\JobResult::initOnce();
        $pool->internalAddGeneratedFile(
            '
�
	Job.protoJobInput.protoJobState.protoJobResult.proto"�
Job

id (	
kind (
runOn (	

expiration (
	timestamp (
input (2	.JobInput
param (2	.JobParam
customerPublicKey (	
description (	
provider	 (	
relays
 (	
result (2
.JobResult
state (2	.JobState
maxExecutionTime (
nodeId (	
outputFormat (	
	encrypted (H �B

_encryptedB.�App\\Grpc\\nostr�App\\Grpc\\nostr\\GPBMetadatabproto3', true);

        static::$is_initialized = true;
    }
}
