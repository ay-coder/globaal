<?php namespace App\Models\CompanyServices\Traits\Relationship;

use App\Models\Services\Services;

trait Relationship
{
	/**
	 * Belongs To relations with Company.
	 *
	 * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
	 */
	public function service()
	{
	    return $this->belongsTo(Services::class, 'service_id');
	}
}