<?php

namespace App\Models;

use App\Traits\ReflectionTrait;
use Illuminate\Database\Eloquent\Model;

class FaqCategory extends Model
{
    use ReflectionTrait;

    protected $fillable = [
        'title',
        'status',
        'locale',
        'order',
    ];

    protected $attributes = [
        'status' => false,
        'order' => 0,
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function questions()
    {
        return $this->hasMany(FaqQuestion::class, 'category_id');
    }
}

