<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserMeetingsOption extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'minimal_price',
        'min_age',
        'max_age',
        'safe_deal_only',
        'photo_verified_only',
        'charity_organization_id'
    ];

    /**
     * Return user associated with meetings options
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Return charity organization associated with user meetings options
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function charity_organization()
    {
        return $this->belongsTo(CharityOrganization::class);
    }


}
