<?php

namespace App\Libraries\Sms;

use Illuminate\Support\Arr;


class MightyText extends AbstractSMS
{
    /**
     * {@inheritDoc}
     */
    protected $apiUrl = 'https://textyserver.appspot.com/client-send-message?function=send&deviceType=ac2dm&client=webapp&client_version=104_20190526&source_client=31';

    /**
     * List of request headers
     *
     * @var array
     */
    protected $requestHeaders = [
        'Accept: */*',
        'Referer: https://mightytext.net/',
        'Origin: https://mightytext.net',
        'User-Agent: Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/69.0.3497.100 Safari/537.36',
        'Content-Type: application/x-www-form-urlencoded; charset=UTF-8',
    ];

    /**
     * MightyText constructor.
     * Add auth cookie to list of headers
     *
     * @param string $SACSID
     */
    protected function __construct(string $SACSID)
    {
        parent::__construct();

        array_push($this->requestHeaders, "Cookie: SACSID=$SACSID");
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

        $postData = "phone=$phone&type=10&deviceType=ac2dm&action=send_sms&action_data=$message";

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
