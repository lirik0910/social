<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserPrivateStreamsOption extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'tariffing',
        'receive_calls',
        'min_age',
        'max_age',
        'photo_verified_only',
        'fully_verified_only',
    ];

    /**
     * Return user who created auction
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

//    /**
//     * Return private stream schedule records for user options
//     *
//     * @return \Illuminate\Database\Eloquent\Relations\HasMany
//     */
//    public function schedules()
//    {
//        return $this->hasMany(UserPrivateStreamsSchedule::class);
//    }
}
