<?php

namespace App\GraphQL\Mutations\Auth;

use App\Models\User;
use App\Rules\Recaptcha;
use App\Traits\RequestDataValidate;
use Firebase\JWT\JWT;
use GraphQL\Type\Definition\ResolveInfo;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Request;
use Illuminate\Validation\ValidationException;
use Joselfonseca\LighthouseGraphQLPassport\Exceptions\ValidationException as GraphQLValidationException;
use Nuwave\Lighthouse\Exceptions\AuthenticationException;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;

use App\Helpers\PhoneHelper;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class Login extends \Joselfonseca\LighthouseGraphQLPassport\GraphQL\Mutations\Login
{
    use RequestDataValidate;

    /**
     * Login
     *
     * @param  null  $rootValue Usually contains the result returned from the parent field. In this case, it is always `null`.
     * @param  mixed[]  $args The arguments that were passed into the field.
     * @param  \Nuwave\Lighthouse\Support\Contracts\GraphQLContext  $context Arbitrary data that is shared between all fields of a single query.
     * @param  \GraphQL\Type\Definition\ResolveInfo  $resolveInfo Information about the query itself, such as the execution state, the field name, path to the field from the root, and more.
     * @return mixed
     * @throws AuthenticationException
     * @throws \Joselfonseca\LighthouseGraphQLPassport\Exceptions\ValidationException
     */
    public function resolve($rootValue, array $args, GraphQLContext $context = null, ResolveInfo $resolveInfo)
    {
        try {
            $inputs = $this->validatedData($args['data'], $this->getValidationRules());
        }  catch (ValidationException $e) {
            throw new GraphQLValidationException($e->errors(), __('Input validation failed.'));
        }

        try {
            // retrieve auth credentials
            $credentials = $this->buildCredentials($inputs);

            // Laravel passport oAuth
            $authData = $this->makeRequest($credentials);
            // fetching access token from auth payload
            $authToken = data_get($authData, 'access_token');
            // get user by token
            $user = $this->getUser($authToken);

            if ($user->hasFlag(User::FLAG_ENABLED_PHONE_VERIFICATION)) {

                $user->addFlag(User::FLAG_REQUIRED_PHONE_VERIFICATION);

                // TODO: remove checking on test environment
                $isTestEnvironment = env('APP_ENV') != "production";

                $verificationCode = $isTestEnvironment ? config('app.phone_verification_default_code') : PhoneHelper::generateVerificationCode();

                $user->phone_verification_code = Hash::make($verificationCode);
                $user->phone_verification_expired_at = Carbon::now()->addSeconds(User::EXPIRED_TOKEN_TIMEOUT);

                $user->save();

                // once user stored successfully send verification code and return auth data
                if (!$isTestEnvironment) {
                    $user->sendSms(__('sms.verification', ['code' => $verificationCode]));
                }
            }

            return [
                'token' => $authToken,
                'user'  => $user,
            ];
        } catch (AuthenticationException $e) {
            throw new AuthenticationException(__($e->getMessage()));
        }
    }

    /**
     * @inheritdoc
     */
    public function buildCredentials(array $inputs = [], $grantType = "password")
    {
        $credentials['username'] = Arr::get($inputs, 'phone');
        $credentials['password'] = Arr::get($inputs, 'password');
        $credentials['client_id'] = config('lighthouse-graphql-passport.client_id');
        $credentials['client_secret'] = config('lighthouse-graphql-passport.client_secret');
        $credentials['grant_type'] = $grantType;

        return $credentials;
    }

    /**
     * Get user from JWT token
     *
     * @param string $authToken
     *
     * @return \App\Models\User
     * @throws \DomainException
     * @throws \Nuwave\Lighthouse\Exceptions\AuthenticationException
     */
    protected function getUser($authToken)
    {
        list($headb64, $bodyb64, $cryptob64) = explode('.', $authToken);

        $body = JWT::jsonDecode(JWT::urlsafeB64Decode($bodyb64));
        $identity = data_get($body, 'sub');

        if (!$identity) {
            throw new AuthenticationException(__('The user credentials were incorrect.'));
        }

        return User::find($identity);
    }

    /**
     * Return validation rules for login
     *
     * @return array
     */
    protected function getValidationRules()
    {
        $rules = [
            'password' => 'required|string|min:8|max:22|regex:/^[a-zA-Z\d@\!\?#\+\-$%^{}\[\]\(\)\~\,\;\:\.\<\>\'\\\"\/\&\*\`]{8,22}$/',
            'phone' => 'required|string|max:19|regex:/^\+\d+$/i',
            'recaptcha' => ['required', new Recaptcha()],
        ];

        $app_client_header = Request::header('App-Client');
        $mobile_client_header_exists = !empty($app_client_header) && in_array($app_client_header, ['android', 'ios']);

        if (env('APP_ENV') != "production" || $mobile_client_header_exists) {
            Arr::forget($rules, 'recaptcha');
        }

        return $rules;
    }
}
