<?php

namespace App\Models;

use App\Traits\ReflectionTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PaymentPercent extends Model
{
    use SoftDeletes, ReflectionTrait;

    const PAYMENT_PERCENT_ENABLE = true;
    const PAYMENT_PERCENT_DISABLE = false;

    const PAYMENT_PERCENT_MODEL_MEETING = 1;
    const PAYMENT_PERCENT_MODEL_MEDIA_PRESENT = 2;
    const PAYMENT_PERCENT_MODEL_PAYMENT_ORDER = 3;

    const PAYMENT_PERCENT_TYPE_FEE = 1;
    const PAYMENT_PERCENT_TYPE_PENALTY = 2;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'model',
        'type',
        'percent',
        'status',
    ];
}
