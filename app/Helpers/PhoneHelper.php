<?php

namespace App\Helpers;

class PhoneHelper
{
    /**
     * Namespace of class that will be used for sending sms
     *
     * @var string
     */
//    protected static $instanceClass = 'App\Libraries\Sms\SPDevSMS';
    protected static $instanceClass = 'App\Libraries\Sms\ePochtaSMS';

    /**
     * Object of class that will be used for sending sms
     *
     * @var null|object
     */
    protected static $senderInstance = NULL;

    /**
     * List of phone codes by country that provide $instanceClass
     *
     * @return array
     */
    public static function phoneAssoc()
    {
        return self::getInstance()->phoneAssoc();
    }

    /**
     * Return array with available phone codes that provide $instanceClass
     *
     * @return array
     */
    public static function phoneCodes()
    {
        return self::getInstance()->phoneCodes();
    }

    /**
     * Run $instanceClass validation
     *
     * @param string $phoneCode
     * @param string $phoneNumber
     *
     * @return bool
     */
    public static function validatePhone(string $phoneCode, string $phoneNumber)
    {
        return self::getInstance()->validate($phoneCode, $phoneNumber);
    }

    /**
     * Run $instanceClass sending sms functionality
     *
     * @param string $phone
     * @param string $message
     *
     * @return mixed
     */
    public static function sendSms(string $phone, string $message)
    {
        return self::getInstance()->send($phone, $message);
    }

    /**
     * Store $instanceClass instance to $senderInstance (if one is null)
     * and return it
     *
     * @return object
     */
    protected static function getInstance()
    {
        if (!self::$senderInstance)
            self::$senderInstance = (self::$instanceClass)::init(env('SMS_ACCESS_KEY'));

        return self::$senderInstance;
    }

    /**
     * Generate verification code for sms sending
     *
     * @return mixed
     */
    public static function generateVerificationCode()
    {
        return (string) rand(100000, 999999);
    }
}
