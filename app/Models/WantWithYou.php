<?php

namespace App\Models;

use App\Traits\ReflectionTrait;
use Illuminate\Database\Eloquent\Model;

class WantWithYou extends Model
{
    use ReflectionTrait;

    const TYPE_MEETING = 1;

    protected $table = 'want_with_you';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'type',
        'user_id',
    ];

    /**
     * Get user who want something from another user
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function who_want()
    {
        return $this->belongsTo(User::class, 'who_want_id');
    }

    /**
     * Get user who receive the request about wants
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function receiver()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
