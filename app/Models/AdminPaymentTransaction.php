<?php

namespace App\Models;

use App\Traits\ReflectionTrait;
use Illuminate\Database\Eloquent\Model;

class AdminPaymentTransaction extends Model
{
    use ReflectionTrait;

    const TYPE_IN = 1;
    const TYPE_OUT = 2;

    /**
     * Return user which balance was changed
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Return admin which make changes
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function admin()
    {
        return $this->belongsTo(User::class, 'admin_id');
    }
}
