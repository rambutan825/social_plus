<?php

namespace Zhiyi\Plus\Models;

use Zhiyi\Plus\Models\User;
use Illuminate\Database\Eloquent\Model;

/**
* 
*/
class Taggable extends Model
{
	protected $table = 'taggables';

	public function user()
	{
		return $this->belongsTo(User::class, 'taggable_id', 'id');
	}
}