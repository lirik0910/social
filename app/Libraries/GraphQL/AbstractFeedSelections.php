<?php


namespace App\Libraries\GraphQL;


use App\Exceptions\GraphQLLogicRestrictException;
use App\Helpers\CoordinatesDistanceHelper;
use App\Models\Profile;
use App\Models\User;
use App\Traits\DynamicValidation;
use App\Traits\ReflectionTrait;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;



abstract class AbstractFeedSelections extends AbstractSelection
{
    use ReflectionTrait, DynamicValidation;

    const CONTENT_TYPE_AUCTIONS = 'auctions';
    const CONTENT_TYPE_ADVERTS = 'adverts';
    const CONTENT_TYPE_MEDIA = 'media';

    /**
     * Authorized user
     *
     * @var User
     */
    protected $user;

    /**
     * Authorized user`s profile
     *
     * @var Profile
     */
    protected $profile;

    /**
     * Array with auctions ids which was viewed on previous pages
     *
     * @var array
     */
    protected $viewed_ids = [];

    /**
     * Content variety depending of user dating preference
     *
     * @var float
     */
    protected $content_variety;

    /**
     * Querying content type`s name
     *
     * @var string
     */
    protected $content_type;

    /**
     * Boundary coordinates for content searching
     *
     * @var array
     */
    protected $boundary_coordinates;

    /**
     * Location radius value for content searching
     *
     * @var integer
     */
    protected $content_search_radius;

    /**
     * Set properties that is needed for each feed query
     *
     * @param array $inputs
     */
    protected function setMainProperties(array $inputs)
    {
        $this->user = Auth::user();
        $this->profile = $this->user->profile;
        $this->content_search_radius = Arr::get($inputs, 'search_radius');

        !empty($inputs['location']) ? $this->setBoundaryCoordinates($inputs['location']) : $this->setBoundaryCoordinates();

        $this->viewed_ids = $inputs['viewed_ids'] ?? [];

        $this->setContentVariety();

        $this->pagination = Arr::only($inputs, ['limit']);
    }

    /**
     * Get results for query
     *
     * @return Collection|mixed
     * @throws GraphQLLogicRestrictException
     */
    protected function getResultsCustom()
    {
        //$coordinates_distance_clause = DB::raw('acos(sin(radians('. $this->profile->lat .'))*sin(radians(location_lat)) + cos(radians(' . $this->profile->lat . '))*cos(radians(location_lat))*cos(radians(location_lng)-radians('. $this->profile->lng .'))) *' . CoordinatesDistanceHelper::EARTH_RADIUS . '<=' . self::CONTENT_SEARCH_RADIUS);

        $sub_query_limit = (int) round($this->pagination['limit'] * $this->content_variety, 0, PHP_ROUND_HALF_DOWN);
        $query_limit = (int) $this->pagination['limit'] - round($this->pagination['limit'] * $this->content_variety, 0, PHP_ROUND_HALF_DOWN);

        if(!$this->content_type) {
            throw new GraphQLLogicRestrictException(__('common.content_type_missing'), __('Error'));
        }

        $sub_query = clone $this->getBaseQuery()
            ->where(function ($q) {
                if($this->profile->dating_preference == Profile::PREFERENCE_ALL){
                    $q->where('profiles.sex', Profile::GENDER_MALE);
                } else {
                    $q->where('profiles.sex', '!=', $this->profile->dating_preference);
                }
            })
            ->whereNotIn($this->content_type . '.id', $this->viewed_ids)
            ->limit($sub_query_limit);

        $query = clone $this->getBaseQuery()
            ->where(function ($q) {
                if($this->profile->dating_preference == Profile::PREFERENCE_ALL){
                    $q->where('sex', Profile::GENDER_FEMALE);
                } else {
                    $q->where('sex', $this->profile->dating_preference);
                }
            })
            ->whereNotIn($this->content_type . '.id', $this->viewed_ids)
            ->limit($query_limit)
            ->union($sub_query);

        $results = $query->get();

        $this->setViewedIds($results);

        if($results->count() < $this->pagination['limit']) {
            $this->pagination['limit'] -= $results->count();
            $additional = $this->getBaseQuery()
                ->limit($this->pagination['limit'])
                ->whereNotIn($this->content_type . '.id', $this->viewed_ids)
                ->get();

            $results = $results->merge($additional);
        }

        return $results->shuffle();
    }

    /**
     * Set content`s ids which was viewed
     *
     * @param Collection $new_content
     * @return void|array
     */
    public function setViewedIds(Collection $new_content)
    {
        $new_ids = $new_content
            ->map(function ($item) {
                return $item->id;
            })
            ->toArray();

        if(!empty($this->viewed_ids)) {
            $ids = array_merge($this->viewed_ids, $new_ids);
        } else {
            $ids = $new_ids;
        }

        return $this->viewed_ids = $ids;
    }

    /**
     * Set boundary coordinates for content`s selection
     *
     * @param array $location
     */
    protected function setBoundaryCoordinates(array $location = [])
    {
        if(!empty($this->content_search_radius)) {
            if(!empty($location)) {
                $this->boundary_coordinates = CoordinatesDistanceHelper::calculateBoundaryCoordinates($location['lat'], $location['lng'], $this->content_search_radius);
            } elseif(!empty($this->profile->lat) && !empty($this->profile->lng)) {
                $this->boundary_coordinates = CoordinatesDistanceHelper::calculateBoundaryCoordinates($this->profile->lat, $this->profile->lng, $this->content_search_radius);
            }
        }
    }

    /**
     * Set content`s selection variety dependent of user`s dating preference
     */
    protected function setContentVariety()
    {
        if(!$this->profile->dating_preference || $this->profile->dating_preference == Profile::PREFERENCE_ALL){
            $this->content_variety = 0.5;
        } else {
            $this->content_variety = 0.4;
        }
    }
}
