<?php

namespace Zhiyi\Plus\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class UserGroupLink extends Model
{
    public function userGroup()
    {
        return $this->belongsTo(UserGroup::class, 'id', 'user_group_id');
    }

    public function scopeByUserId(Builder $query, string $user_id): Builder
    {
        return $query->where('user_id', $user_id);
    }
}
