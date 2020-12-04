<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PasswordReset extends Model
{
    public $incrementing = false;
    public $timestamps = false;

    protected $primaryKey = 'phone';
    protected $keyType = 'string';

    /**
     * The attributes that should be converted to Carbon instance.
     *
     * @var array
     */
    protected $dates = [
        'expired_at',
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'phone',
        'expired_at',
        'token',
    ];

}
