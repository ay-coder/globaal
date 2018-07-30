<?php namespace App\Models\Companies\Traits\Relationship;

use App\Models\Access\User\User;

trait Relationship
{
	/**
	 * Belongs To relations with Company.
	 *
	 * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
	 */
	public function user()
	{
	    return $this->belongsTo(User::class);
	}
}