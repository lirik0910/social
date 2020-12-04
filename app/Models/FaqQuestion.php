<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FaqQuestion extends Model
{
    protected $fillable = [
        'category_id',
        'title',
        'content',
        'status',
        'order',
    ];

    protected $attributes = [
        'status' => false,
        'order' => 0,
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function category()
    {
        return $this->belongsTo(FaqCategory::class, 'category_id');
    }
}
