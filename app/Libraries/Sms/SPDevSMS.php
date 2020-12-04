<?php

namespace App\Libraries\Sms;

use Illuminate\Support\Arr;


class SPDevSMS extends AbstractSMS
{
    /**
     * {@inheritDoc}
     */
    protected $apiUrl = 'http://sms.buy-dating.com/api/resource/';

    /**
     * List of request headers
     *
     * @var array
     */
    protected $requestHeaders = [
        'Content-Type: application/json; charset=UTF-8',
    ];

    /**
     * SPDevSMS constructor.
     * Add device id to request url
     *
     * @param string $deviceId
     */
    protected function __construct(string $deviceId)
    {
        parent::__construct();

        $this->apiUrl .= $deviceId;
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

        $postData = json_encode([
            'n' => $phone,
            'm' => $message
        ]);

        array_push($this->requestHeaders, 'Content-Length: ' . strlen($postData));

        curl_setopt($this->ch, CURLOPT_URL, $this->apiUrl);
        curl_setopt($this->ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($this->ch, CURLOPT_POSTFIELDS, $postData);
        curl_setopt($this->ch, CURLOPT_POST, 1);
        curl_setopt($this->ch, CURLOPT_HTTPHEADER, $this->requestHeaders);
    }

    /**
     * {@inheritDoc}
     */
    public function phoneAssoc()
    {
        return [
            'ua' => ['name' => 'Ukraine', 'dial_code' => '+380', 'code' => 'ua'],
            'ug' => ['name' => 'Uganda', 'dial_code' => '+256', 'code' => 'ug'],
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
