<?php

namespace App\Models;

use App\Exceptions\GraphQLLogicRestrictException;
use App\Traits\ReflectionTrait;
use App\Helpers\MediaHelper;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Arr;

class Profile extends Model
{
    use ReflectionTrait;

    const MIN_AVAILABLE_AGE = 18;
    const MAX_AVAILABLE_AGE = 100;

    const GENDER_MALE = 1;
    const GENDER_FEMALE = 2;

    const PREFERENCE_ALL = 1;
    const PREFERENCE_MALE = 2;
    const PREFERENCE_FEMALE = 3;

    const PHYSIQUE_SKINNY = 1;
    const PHYSIQUE_SLIM = 2;
    const PHYSIQUE_ATHLETIC = 3;
    const PHYSIQUE_MUSCULAR = 4;
    const PHYSIQUE_PLUMP = 5;
    const PHYSIQUE_OVERWEIGHT = 6;

    const APPEARANCE_AFRICAN = 1;
    const APPEARANCE_ASIAN = 2;
    const APPEARANCE_EUROPEAN = 3;
    const APPEARANCE_METISE = 4;
    const APPEARANCE_MULATTO = 5;

    const EYE_COLOR_BLUE = 1;
    const EYE_COLOR_BROWN = 2;
    const EYE_COLOR_GREEN = 3;
    const EYE_COLOR_GREY = 4;
    const EYE_COLOR_HETEROCHROMIA = 5;

    const HAIR_COLOR_BLACK = 1;
    const HAIR_COLOR_BLOND = 2;
    const HAIR_COLOR_BROWN = 3;
    const HAIR_COLOR_GREY = 4;
    const HAIR_COLOR_RED = 5;
    const HAIR_COLOR_UNCOMMON = 6;

    const MARITAL_STATUS_SEARCHING = 1;
    const MARITAL_STATUS_SINGLE = 2;
    const MARITAL_STATUS_HAVE_PARTNER = 3;
    const MARITAL_STATUS_MARRIED = 4;
    const MARITAL_STATUS_SEPARATED = 5;
    const MARITAL_STATUS_DIVORCED = 6;
    const MARITAL_STATUS_WIDOWED = 7;

    const SMOKING_POSITIVE = 1;
    const SMOKING_NEUTRAL = 2;
    const SMOKING_NEGATIVE = 3;

    const ALCOHOL_POSITIVE = 1;
    const ALCOHOL_NEUTRAL = 2;
    const ALCOHOL_NEGATIVE = 3;

    const KIDS_YES = 1;
    const KIDS_NO = 0;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'age',
        'sex',
        'dating_preference',
        'country',
        'region',
        'address',
        'lat',
        'lng',
        'name',
        'surname',
        'height',
        'physique',
        'appearance',
        'eye_color',
        'hair_color',
        'occupation',
        'marital_status',
        'kids',
        'languages',
        'smoking',
        'alcohol',
        'about',
        'chat_price'
    ];

    protected $guest_available = [
        'age',
        'sex',
        'dating_preference',
        'height',
        'physique',
        'appearance',
        'eye_color',
        'hair_color',
        'occupation',
        'marital_status',
        'kids',
        'languages',
        'smoking',
        'alcohol',
        'about',
    ];

    /**
     * The attributes that should be converted to Carbon instance.
     *
     * @var array
     */
    protected $dates = [
        'age'
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'languages' => 'array',
    ];

    /**
     * Return user associated with profile
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Return user years
     *
     * @return int
     */
    public function getYearsAttribute()
    {
        return (int) Carbon::parse($this->age)->floatDiffInYears(now());
    }

    /**
     * Return public uri for profiles background
     *
     * @return string
     * @throws GraphQLLogicRestrictException
     */
    public function getBackgroundUriAttribute()
    {
        $root_path = MediaHelper::getS3Path(MediaHelper::FILE_TYPE_PROFILE_BACKGROUND);

        $s3path = [
            'custom' => $root_path . '/users/' . $this->profile_background,
            'default' => $root_path . '/' . $this->profile_background
        ];

        foreach($s3path as $path) {
            if(MediaHelper::checkExists($path)) {
                $uri = MediaHelper::getPublicUrl($path);
            }
        }

        return $uri ?? null;
    }
}
