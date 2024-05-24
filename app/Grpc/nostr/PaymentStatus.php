<?php

// Generated by the protocol buffer compiler.  DO NOT EDIT!
// source: PaymentStatus.proto

namespace App\Grpc\nostr;

use UnexpectedValueException;

/**
 * Protobuf type <code>PaymentStatus</code>
 */
class PaymentStatus
{
    /**
     * Generated from protobuf enum <code>PAYMENT_SENT = 0;</code>
     */
    const PAYMENT_SENT = 0;

    /**
     * Generated from protobuf enum <code>PAYMENT_RECEIVED = 1;</code>
     */
    const PAYMENT_RECEIVED = 1;

    /**
     * Generated from protobuf enum <code>PAYMENT_REFUNDED = 2;</code>
     */
    const PAYMENT_REFUNDED = 2;

    /**
     * Generated from protobuf enum <code>PAYMENT_FAILED = 3;</code>
     */
    const PAYMENT_FAILED = 3;

    /**
     * Generated from protobuf enum <code>PAYMENT_PENDING = 4;</code>
     */
    const PAYMENT_PENDING = 4;

    /**
     * Generated from protobuf enum <code>PAYMENT_UNKNOWN_STATUS = 99;</code>
     */
    const PAYMENT_UNKNOWN_STATUS = 99;

    private static $valueToName = [
        self::PAYMENT_SENT => 'PAYMENT_SENT',
        self::PAYMENT_RECEIVED => 'PAYMENT_RECEIVED',
        self::PAYMENT_REFUNDED => 'PAYMENT_REFUNDED',
        self::PAYMENT_FAILED => 'PAYMENT_FAILED',
        self::PAYMENT_PENDING => 'PAYMENT_PENDING',
        self::PAYMENT_UNKNOWN_STATUS => 'PAYMENT_UNKNOWN_STATUS',
    ];

    public static function name($value)
    {
        if (! isset(self::$valueToName[$value])) {
            throw new UnexpectedValueException(sprintf(
                'Enum %s has no name defined for value %s', __CLASS__, $value));
        }

        return self::$valueToName[$value];
    }

    public static function value($name)
    {
        $const = __CLASS__.'::'.strtoupper($name);
        if (! defined($const)) {
            throw new UnexpectedValueException(sprintf(
                'Enum %s has no value defined for name %s', __CLASS__, $name));
        }

        return constant($const);
    }
}
