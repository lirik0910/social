<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SupportMessage extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'support_id',
        'user_id',
        'support_user_id',
        'message',
    ];

    /**
     * Get support
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function support()
    {
        return $this->belongsTo(Support::class, 'support_id', 'id');
    }

    /**
     * Get user
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    /**
     * Get user
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function support_user()
    {
        return $this->belongsTo(User::class, 'support_user_id', 'id');
    }
}
