<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class UserPrivateStreamsSchedule extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'weekday',
        'period_from',
        'period_to',
    ];

    /**
     * The attributes that should be converted to Carbon instance.
     *
     * @var array
     */
    protected $dates = [
 #       'period_from',
 #       'period_to',
    ];

    /**
     * Return user options which associated with schedule
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function options()
    {
        return $this->belongsTo(UserPrivateStreamsOption::class);
    }
}
