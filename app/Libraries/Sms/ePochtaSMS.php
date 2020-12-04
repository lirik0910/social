<?php

namespace App\Libraries\Sms;

use Illuminate\Support\Arr;

class ePochtaSMS extends AbstractSMS
{
    /**
     * {@inheritDoc}
     */
    protected $apiUrl = 'http://api.atompark.com/members/sms/xml.php';

    /**
     * List of API errors
     *
     * @var array
     */
    protected $apiError = [
        "-1" => [
            'status' => '-1',
            'code' => 'AUTH_FAILED',
            'message' => 'Неправильний логін та/або пароль',
        ],
        "-2" => [
            'status' => '-2',
            'code' => 'XML_ERROR',
            'message' => 'Неправильний формат XML',
        ],
        "-3" => [
            'status' => '-3',
            'code' => 'NOT_ENOUGH_CREDITS',
            'message' => 'Недостатньо кредитів на акаунті користувача',
        ],
        "-4" => [
            'status' => '-4',
            'code' => 'NO_RECIPIENTS',
            'message' => 'Немає вірних номерів отримувачів',
        ],
        "-7" => [
            'status' => '-7',
            'code' => 'BAD_SENDER_NAME',
            'message' => 'Помилка в імені відправника',
        ],
    ];

    /**
     * SPDevSMS constructor.
     */
    protected function __construct()
    {
        parent::__construct();
    }
    /**
     * {@inheritDoc}
     */
    public function validate(string $phoneCode, string $phoneNumber)
    {
        if (!in_array($phoneCode, $this->phoneCodes()))
            return false;

        if (!$phoneNumber || !preg_match('/^((50)|(63)|(66)|(67)|(68)|(73)|(93)|(95)|(96)|(97)|(98)|(99))\d{7}$/', $phoneNumber)) {
            return false;
        }

        if (strlen($phoneNumber) !== 9) {
            return false;
        }

        return true;
    }

    /**
     * Configure request before executing
     *
     * @param mixed ...$args
     *
     * @return void
     */
    protected function configureRequest(...$args)
    {
        list($phone, $message) = $args;

        // info for logs
        $this->phone = $phone;

        $postData = '<?xml version="1.0" encoding="UTF-8"?>';
        $postData = $postData . "<SMS>
            <operations>
                <operation>SEND</operation>
            </operations>
            <authentification>
                <username>" . env('E_POCHTA_LOGIN') . "</username>
                <password>" . env('E_POCHTA_PASSWORD') . "</password>
            </authentification>
            <message>
                <sender>" . env('E_POCHTA_SENDER') . "</sender>
                <text>" . $message . "</text>
            </message>
            <numbers>
                <number>" . $phone ."</number>
            </numbers>
        </SMS>";

        curl_setopt($this->ch, CURLOPT_URL, $this->apiUrl);
        curl_setopt($this->ch, CURLOPT_FOLLOWLOCATION, false);
        curl_setopt($this->ch, CURLOPT_POST, true);
        curl_setopt($this->ch, CURLOPT_HEADER, false);
        curl_setopt($this->ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($this->ch, CURLOPT_CONNECTTIMEOUT,15);
        curl_setopt($this->ch, CURLOPT_TIMEOUT,100);
        curl_setopt($this->ch, CURLOPT_POSTFIELDS, ['XML' => $postData]);

    }

    /**
     * {@inheritDoc}
     */
    public function phoneAssoc()
    {
        return [
            'ua' => ['name' => 'Ukraine', 'dial_code' => '+380', 'code' => 'ua'],
//            'ug' => ['name' => 'Uganda', 'dial_code' => '+256', 'code' => 'ug'],
        ];
    }

    /**
     * {@inheritDoc}
     */
    public function phoneCodes()
    {
        return Arr::pluck(self::phoneAssoc(), 'dial_code');
    }

}


