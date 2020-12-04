<?php

namespace App\Models\AbstractModels;

use App\Models\User;

class ProtectedVisibilityAuction extends ProtectedVisibility
{
    public function setProtected($user)
    {
        // TODO delete a condition with a profile existence check
        if($user->profile) {
            if ($this->min_age > $user->profile->years || $this->max_age < $user->profile->years) {
                $this->protected_visibility_status = 0;
                $this->protected_visibility_reasons = __('auction.age_restrictions');
            }
        }
        if ($this->photo_verified_only == true && !$user->hasFlag(User::FLAG_PHOTO_VERIFIED)) {
            $this->protected_visibility_status = 0;
            $this->protected_visibility_reasons = __('auction.photo_verification_required');
        }
        if($this->location_for_winner_only == true) { // TODO: check if user is winner
            $this->location_lat = null;
            $this->location_lng = null;
            $this->address = null;
        }

        return $this;
    }
}
