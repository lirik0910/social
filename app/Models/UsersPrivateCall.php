<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UsersPrivateCall extends Model
{
    const ACTION_CALL = 1;
    const ACTION_ACCEPT = 2;
    const ACTION_REJECT = 3;
    const ACTION_END = 4;
    const ACTION_MISS = 5;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'parent_id',
        'caller_user_id',
        'callee_user_id',
        'action',
        'status',
        'meeting_id',
    ];

    /**
     * Get all actions of the call.
     */
    public function answers()
    {
        return $this->hasMany(UsersPrivateCall::class, 'parent_id');
    }

    /**
     * Return call associated action
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function parent()
    {
        return $this->belongsTo(UsersPrivateCall::class, 'parent_id', 'id');
    }

    /**
     * Return caller user associated with user
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function caller()
    {
        return $this->belongsTo(User::class, 'caller_user_id', 'id');
    }

    /**
     * Return callee user associated with user
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function callee()
    {
        return $this->belongsTo(User::class, 'callee_user_id', 'id');
    }

    /**
     * Return callee user associated with user
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function meeting()
    {
        return $this->belongsTo(Meeting::class, 'id', 'meeting_id');
    }

}
