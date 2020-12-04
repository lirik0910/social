<?php


namespace App\Libraries\Payment;

use App\Models\PaymentTransaction;
use Illuminate\Support\Arr;

class FourBill
{
    const SERVICE_INPUT = 2;
    const SERVICE_OUTPUT = 3;

    /**
     * cUrl resource
     *
     * @var false|resource|null
     */
    protected $ch = NULL;

    /**
     * Self instance
     *
     * @var null|self
     */
    protected static $self = NULL;


    /**
     * Payment base url
     *
     * @var string
     */
    private $apiUrl = 'https://api.4bill.io';

    /**
     * Rate token to currency in coins
     *
     * @var int
     */
    private $exchangeRate = 25; // TODO: configure from admin dashboard or from bank API

    /**
     * Currency of payment system
     *
     * @var string
     */
    private $exchangeCurrency = 'UAH';

    /**
     * FourBill constructor. Init cURL.
     */
    protected function __construct()
    {
        $this->ch = curl_init();
        curl_setopt($this->ch, CURLOPT_CONNECTTIMEOUT, 5);
        curl_setopt($this->ch, CURLOPT_TIMEOUT, 30);
        curl_setopt($this->ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($this->ch, CURLOPT_POST, 1);
    }

    /**
     * Get instance of current class
     *
     * @return self|null
     */
    public static function init()
    {
        if (!self::$self)
            self::$self = new static();

        return self::$self;
    }

    /**
     * Info about payment account
     *
     * @return array
     * @throws FourBillException
     */
    public function accountInfo()
    {
        $requestUrl = "{$this->apiUrl}/account/info";

        return $this->sendRequest($requestUrl, $this->generateAuthData());
    }

    /**
     * Validate transaction
     *
     * @return bool
     * @throws FourBillException
     */
    public function transactionValidate(): bool
    {
        $requestUrl = "{$this->apiUrl}/transaction/validate";

        $settings = [
            'external_transaction_id' => 1,
            'customer_ip_address' => '127.0.0.1',
            'account_id' => env('4BILL_ACCOUNT'),
            'wallet_id' => env('4BILL_WALLET'),
            'service_id' => self::SERVICE_INPUT,
            'amount' => 1000,
            'amount_currency' => 'UAH',
        ];

        $data = array_merge($this->generateAuthData(), $settings);

        $response = $this->sendRequest($requestUrl, $data);

        if ($response) {
            // TODO: Handle all statuses
            return $response['status'] == 1; // success
        }

        return false;
    }

    /**
     * Create external transaction
     *
     * @param PaymentTransaction $transaction
     * @param string|null $cardNumber
     *
     * @return array
     * @throws FourBillException
     */
    public function transactionCreate(PaymentTransaction $transaction, string $cardNumber = null): array
    {
        $requestUrl = "{$this->apiUrl}/transaction/create";

        if ($transaction->type === PaymentTransaction::TRANSACTION_TYPE_OUT) {
            $amount = empty($transaction->value) ? $transaction->amount : $transaction->amount - $transaction->value;
        } else {
            $amount = $transaction->amount;
        }

        $settings = [
            'external_transaction_id' => $transaction->id,
            'external_order_id' => $transaction->source_id,
            'external_customer_id' => $transaction->from_user_id,
            'customer_ip_address' => request()->ip(),
            'account_id' => env('4BILL_ACCOUNT'),
            'wallet_id' => env('4BILL_WALLET'),
            'service_id' => $transaction->type == PaymentTransaction::TRANSACTION_TYPE_IN ? self::SERVICE_INPUT : self::SERVICE_OUTPUT,
            'amount' => $amount * $this->exchangeRate,
            'amount_currency' => $this->exchangeCurrency,
            'description' => '',
            'point' => [
                'callback_url' => url('/api/payment/4bill/{transaction_id}/check')
            ],
        ];

        if ($cardNumber) {
            $settings['fields'] = ['card_number' => $cardNumber];
        }

        $data = array_merge($this->generateAuthData(), $settings);

        $result = $this->sendRequest($requestUrl, $data);

        if($transaction->type === PaymentTransaction::TRANSACTION_TYPE_OUT) {
            $this->transactionConfirm($result['id']);
        }

        return array_merge(
            Arr::only($result, ['id', 'amount', 'status']),
            [
                'pay_url' => $result['result']['pay_url'],
                'currency' => $this->exchangeCurrency,
            ]
        );
    }

    /**
     * Looking transaction in payment system
     *
     * @param int $transactionId
     *
     * @return array
     * @throws FourBillException
     */
    public function transactionFind(int $transactionId): array
    {
        $requestUrl = "{$this->apiUrl}/transaction/find";

        $data = array_merge($this->generateAuthData(), [
            'id' => $transactionId,

        ]);

        $result = $this->sendRequest($requestUrl, $data);

        return Arr::only($result, ['id', 'amount', 'status']);
    }

    /**
     * Return internal status based on external
     *
     * @param int $externalStatus
     *
     * @return int
     */
    public static function externalStatusToInternal(int $externalStatus): int
    {
        $internalStatus = NULL;

        switch ($externalStatus) {
            case -1:
            case 0:
                $internalStatus = PaymentTransaction::TRANSACTION_STATUS_PENDING;
                break;
            case 1:
                $internalStatus = PaymentTransaction::TRANSACTION_STATUS_COMPLETED;
                break;
            case 2:
            case 3:
            case 4:
            case 5:
                $internalStatus = PaymentTransaction::TRANSACTION_STATUS_FAILED;
                break;
            default:
                $internalStatus = PaymentTransaction::TRANSACTION_STATUS_PENDING;
        }

        return $internalStatus;
    }

    /**
     * Confirm transaction (only for PaymentOrderOut)
     *
     * @param int $transactionId
     * @return array
     * @throws FourBillException
     */
    public function transactionConfirm(int $transactionId) : array
    {
        $requestUrl = "{$this->apiUrl}/transaction/confirm";

        $data = array_merge($this->generateAuthData(), [
            'id' => $transactionId,

        ]);

        $result = $this->sendRequest($requestUrl, $data);

        return Arr::only($result, ['id', 'amount', 'status']);
    }

    /**
     * General data for each request
     *
     * @param string $locale
     *
     * @return array
     */
    private function generateAuthData(string $locale = 'en'): array
    {
        $point = env('4BILL_POINT');
        $apiKey = env('4BILL_API_KEY');
        $key = microtime();

        return [
            'auth' => [
                'debug' => true,
                'point' => (int)$point,
                'key' => $key,
                'hash' => md5($point . $apiKey . $key),
            ],
            'locale' => $locale,
        ];
    }

    /**
     * Send request to API
     *
     * @param string $requestUrl endpoint url
     * @param array $data request data
     *
     * @return array
     *
     * @throws FourBillException
     */
    private function sendRequest(string $requestUrl, array $data = []) : array
    {
        $jsonData = !empty($data) ? json_encode($data) : '';

        curl_setopt($this->ch, CURLOPT_URL, $requestUrl);
        curl_setopt($this->ch, CURLOPT_POSTFIELDS, $jsonData);
        curl_setopt($this->ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json; charset=UTF-8',
            'Content-Length: ' . strlen($jsonData)
        ]);

        $result = curl_exec($this->ch);

        if (curl_errno($this->ch)) {
            throw new FourBillException(
                curl_error($this->ch),
                -1*curl_errno($this->ch),
                $data
            );
        }

        $result = json_decode($result, true);

        if (Arr::get($result, 'error.code', 0) > 0) {
            throw new FourBillException(
                Arr::get($result, 'error.message'),
                Arr::get($result, 'error.code'),
                $data
            );
        }

        return $result['response'];
    }

    /**
     * Destroy curl resource
     */
    public function __destruct()
    {
        curl_close($this->ch);
    }

}
