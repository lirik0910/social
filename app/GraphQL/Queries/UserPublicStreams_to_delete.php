<?php

namespace App\GraphQL\Queries;

use App\Models\User;
use App\Traits\ReflectionTrait;
use App\Traits\RequestDataValidate;
use GraphQL\Type\Definition\ResolveInfo;
use Illuminate\Validation\ValidationException;
use Joselfonseca\LighthouseGraphQLPassport\Exceptions\ValidationException as GraphQLValidationException;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;

class UserPublicStreams // TODO: confirm deletion
{
    use RequestDataValidate, ReflectionTrait;

    const FILTER_ALL = 1;
    const FILTER_PLANNED = 2;
    const FILTER_FINISHED = 3;

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
     * Selection filter param
     *
     * @var integer
     */
    protected $filter;

    /**
     * Get public streams for personal account page
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
                'count' => 'integer',
                'filter' => 'integer|in:' . implode(',', array_keys(self::availableParams('filter')))
            ]);
        } catch (ValidationException $e) {
            throw new GraphQLValidationException($e->errors(), __('Input validation failed.'));
        }

        $this->user = $context->user();
        $this->page = $inputs['page'] ?? 1;
        $this->filter = $inputs['filter'] ?? 1;

        $public_streams = !empty($inputs['count']) ? $this->getPublicStreams($inputs['count']) : collect([]);

        return $public_streams->count() ? [
            'results' => $public_streams,
            'pagination' => [
                'total' => $public_streams->total(),
                'current_page' => $public_streams->currentPage(),
                'last_page' => $public_streams->lastPage(),
            ]
        ] : $public_streams;
    }

    /**
     * Get public streams from database
     *
     * @param int $count
     * @return mixed
     */
    public function getPublicStreams(int $count)
    {
        $public_streams = $this->user
            ->public_streams()
            ->where(function ($query) {
                if ($this->filter === self::FILTER_PLANNED) {
                    $query->where('planned_at', '!=', null);
                    $query->where('started_at', '=', null);
                } elseif ($this->filter === self::FILTER_FINISHED) {
                    $query->where('ended_at', '!=', null);
                }

                if($this->page > 1) {
                    $query->where('ended_at', '!=', null);
                    $query->orWhere('started_at', '=', null);
                }
            })
            ->orderBy('ended_at', 'ASC')
            ->orderBy('started_at', 'DESC')
            ->orderBy('planned_at', 'ASC')
            ->paginate($count, ['*'], 'page', $this->page);

        return $public_streams;
    }
}
