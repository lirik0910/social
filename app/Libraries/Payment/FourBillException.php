<?php


namespace App\Libraries\Payment;


use Throwable;
use Illuminate\Support\Facades\Log;

class FourBillException extends \Exception
{
    protected $systemMessage = '';
    protected $exceptionMessage = 'Payment System Error';

    public function __construct(string $paymentMessage, int $code, array $params, Throwable $previous = null)
    {
        $this->codeToSystemMessage($code);
        $this->logError($paymentMessage, $params);

        parent::__construct($this->exceptionMessage, $code, $previous);
    }

    protected function codeToSystemMessage(int $code) : void
    {
        $systemMessage = '';
        $exceptionMessage = $this->exceptionMessage;

        switch ($code) {
            case 10:
                $systemMessage = 'Bad request';
                break;
            case 11 :
                $systemMessage = 'Invalid auth';
                break;
            case 12 :
                $systemMessage = 'Incorrect Headers';
                break;
            case 13 :
                $systemMessage = 'Internal error';
                break;
            case 14 :
                $systemMessage = 'Forbidden IP';
                $exceptionMessage = 'Your IP address is not allowed by payment system';
                break;
            case 100 :
                $systemMessage = 'Account not found';
                break;
            case 101 :
                $systemMessage = 'Account already exists';
                break;
            case 102 :
                $systemMessage = 'Wallet already exists';
                break;
            case 103 :
                $systemMessage = 'Invalid wallet currency';
                break;
            case 104 :
                $systemMessage = 'Wallet not found';
                break;
            case 105 :
                $systemMessage = 'Not enough balance';
                break;
            case 106 :
                $systemMessage = 'Currency not supported by wallet';
                break;
            case 107 :
                $systemMessage = 'Unable to convert currency';
                break;
            case 108 :
                $systemMessage = 'A currency is required';
                break;
            case 200 :
                $systemMessage = 'Transaction not found';
                break;
            case 201 :
                $systemMessage = 'Service not found or not active';
                break;
            case 202 :
                $systemMessage = 'Invalid service field value';
                break;
            case 203 :
                $systemMessage = 'Transaction with such external id already exists';
                break;
            case 204 :
                $systemMessage = 'Validation failed';
                break;
            case 205 :
                $systemMessage = 'Reverse could not be done';
                break;
            case 206 :
                $systemMessage = 'Check minimum amount';
                $exceptionMessage = 'Too little amount for transaction';
                break;
            case 207 :
                $systemMessage = 'Check maximum amount';
                $exceptionMessage = 'Too big amount for transaction';
                break;
            case 209 :
                $systemMessage = 'Service not allowed for this wallet';
                break;
            case 210 :
                $systemMessage = 'Method confirm not allowed for this service';
                break;
            case 211 :
                $systemMessage = 'No service commissions for this service or point';
                break;
            case 212 :
                $systemMessage = 'Services logic is missing';
                break;
            case 240 :
                $systemMessage = 'Rejected by AntiFraud';
                $exceptionMessage = 'Request rejected by AntiFraud system';
                break;
            case 241 :
                $systemMessage = 'Temporary AntiFraud check error';
                break;
            default :
                $systemMessage = 'Undefined error';
                break;
        }

        $this->systemMessage = $systemMessage;
        $this->exceptionMessage = $exceptionMessage;
    }

    protected function logError(string $paymentMessage, array $params) : void
    {
        Log::channel('payment')->error(
            self::class . ": [{$this->code}]. {$this->systemMessage}\n Payment message: $paymentMessage \n",
            ['params' => $params]
        );
    }
}
