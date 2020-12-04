<?php

namespace App\Models\AbstractModels;

use Illuminate\Database\Eloquent\Model;

abstract class ProtectedVisibility extends Model
{
    public $protected_visibility_status = true;

    public $protected_visibility_reasons = '';

    /**
     * @return bool
     */
    public function getProtectedVisibilityStatusAttribute()
    {
        return $this->protected_visibility_status;
    }

    /**
     * @return string
     */
    public function getProtectedVisibilityReasonsAttribute()
    {
        return $this->protected_visibility_reasons;
    }

    /**
     * @param $user
     * @return mixed
     */
    abstract public function setProtected($user);
}
