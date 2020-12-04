<?php

namespace App\GraphQL\Queries;

use App\Models\PrivateStream;
use App\Models\User;
use App\Traits\ReflectionTrait;
use App\Traits\RequestDataValidate;
use GraphQL\Type\Definition\ResolveInfo;
use Illuminate\Validation\ValidationException;
use Joselfonseca\LighthouseGraphQLPassport\Exceptions\ValidationException as GraphQLValidationException;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;

class UserPrivateStreams // TODO: confirm deletion
{
    use RequestDataValidate, ReflectionTrait;

    const FILTER_ALL = 1;
    const FILTER_ACCEPTED = 2;
    const FILTER_REJECTED = 3;
    const FILTER_MISSED = 4;

    /**
     * Authorized user
     *
     * @var User
     */
    protected $user;

    /**
     * Page number param
     *
     * @var integer
     */
    protected $page;

    /**
     * Selections filter param
     *
     * @var integer
     */
    protected $filter;

    /**
     * Return a value for the field.
     *
     * @param  null  $rootValue Usually contains the result returned from the parent field. In this case, it is always `null`.
     * @param  mixed[]  $args The arguments that were passed into the field.
     * @param  \Nuwave\Lighthouse\Support\Contracts\GraphQLContext  $context Arbitrary data that is shared between all fields of a single query.
     * @param  \GraphQL\Type\Definition\ResolveInfo  $resolveInfo Information about the query itself, such as the execution state, the field name, path to the field from the root, and more.
     * @return mixed
     * @throws \Joselfonseca\LighthouseGraphQLPassport\Exceptions\ValidationException
     * @throws \ReflectionException
     */
    public function resolve($rootValue, array $args, GraphQLContext $context, ResolveInfo $resolveInfo)
    {
        try {
            $inputs = $this->validatedData($args['data'], [
                'page' => 'integer',
                'filter' => 'integer|in:' . implode(',', array_keys(self::availableParams('filter')))
            ]);
        } catch (ValidationException $e) {
            throw new GraphQLValidationException($e->errors(), __('Input validation failed.'));
        }

        $this->page = $inputs['page'] ?? 1;
        $this->filter = $inputs['filter'] ?? 1;
        $content_count = $args['data']['count'];

        $this->user = $context->user();

        $sell = !empty($content_count['sell']) ? $this->getSellPrivateStreams((int) $content_count['sell']) : collect([]);
        $buy = !empty($content_count['buy']) ? $this->getBuyPrivateStreams((int) $content_count['buy']) : collect([]);

        return [
            'sell' => $sell->count() ? [
                'results' => $sell,
                'pagination' => [
                    'total' => $sell->total(),
                    'current_page' => $sell->currentPage(),
                    'last_page' => $sell->lastPage(),
                ]
            ] : $sell,
            'buy' => $buy->count() ? [
                'results' => $buy,
                'pagination' => [
                    'total' => $buy->total(),
                    'current_page' => $buy->currentPage(),
                    'last_page' => $buy->lastPage(),
                ]
            ] : $buy,
        ];
    }

    public function getBasedQuery()
    {
        $based_query = PrivateStream
            ::where(function ($query) {
                switch($this->filter) {
                    case self::FILTER_ACCEPTED:
                        $query->where('status', PrivateStream::STATUS_ACCEPTED);
                        break;
                    case self::FILTER_REJECTED:
                        $query->where('status', PrivateStream::STATUS_REJECTED);
                        break;
                    case self::FILTER_MISSED:
                        $query->where('status', PrivateStream::STATUS_IGNORED);
                        break;
                    default:
                        break;
                }
            })
            ->orderBy('updated_at', 'DESC');

        return clone $based_query;
    }


    /**
     * Get private streams which sell/sold by user
     *
     * @param int $count
     * @return mixed
     */
    public function getSellPrivateStreams(int $count)
    {
        $private_streams = $this->getBasedQuery()
            ->where('seller_id', $this->user->id)
            ->paginate($count, ['*'], 'page', $this->page);

        return $private_streams;
    }

    /**
     * Get private streams which buy/bought by user
     *
     * @param int $count
     * @return mixed
     */
    public function getBuyPrivateStreams(int $count)
    {
        $private_streams = $this->getBasedQuery()
            ->where('user_id', $this->user->id)
            ->paginate($count, ['*'], 'page', $this->page);

        return $private_streams;
    }
}
