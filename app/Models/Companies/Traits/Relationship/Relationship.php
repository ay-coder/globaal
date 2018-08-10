<?php namespace App\Models\Companies\Traits\Relationship;

use App\Models\Access\User\User;
use App\Models\Providers\Providers;
use App\Models\CompanyProviders\CompanyProviders;

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
	
	/**
	 * Providers
	 * 
	 * @return  \Illuminate\Database\Eloquent\Relations\HasMany
	 */
	public function company_providers()
	{
		return $this->hasMany(CompanyProviders::class, 'company_id');	
	}
}