<?php

namespace App\Models;

use App\Traits\ReflectionTrait;
use Illuminate\Database\Eloquent\Model;

class UserBan extends Model
{
    use ReflectionTrait;

    const REASON_REPORTS = 'A lot of reports';
    const REASON_FRAUDER = 'User is frauder';

    const BAN_PERIOD_WEEK = 1;
    const BAN_PERIOD_MONTH = 2;

    /**
     * Return banned user
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
