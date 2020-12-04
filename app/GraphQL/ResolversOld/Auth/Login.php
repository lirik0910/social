<?php

namespace App\GraphQl\ResolversOld\Auth;


use App\Models\User;
use GraphQL\Type\Definition\ResolveInfo;
use Nuwave\Lighthouse\Exceptions\AuthenticationException;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;
use Firebase\JWT\JWT;

class Login extends \Joselfonseca\LighthouseGraphQLPassport\GraphQL\Mutations\Login
{
    /**
     * @param                                                          $rootValue
     * @param array                                                    $args
     * @param \Nuwave\Lighthouse\Support\Contracts\GraphQLContext|NULL $context
     * @param \GraphQL\Type\Definition\ResolveInfo                     $resolveInfo
     *
     * @return array
     * @throws \Nuwave\Lighthouse\Exceptions\AuthenticationException
     */
    public function resolve($rootValue, array $args, GraphQLContext $context = NULL, ResolveInfo $resolveInfo)
    {

        try {
            // retrieve auth credentials
            $credentials = $this->buildCredentials($args);

            // Laravel passport oAuth
            $authData = $this->makeRequest($credentials);
            // fetching access token from auth payload
            $authToken = array_get($authData, 'access_token');
            // get user by token
            $user = $this->getUser($authToken);

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
    public function buildCredentials(array $args = [], $grantType = "password")
    {
        $inputs = collect($args)->get('data');
        $credentials['username'] = $inputs['phone'];
        $credentials['password'] = $inputs['password'];
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
}
