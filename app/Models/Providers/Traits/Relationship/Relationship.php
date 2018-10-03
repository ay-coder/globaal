<?php namespace App\Models\Providers\Traits\Relationship;

use App\Models\Access\User\User;
use App\Models\ProviderServices\ProviderServices;
use App\Models\Companies\Companies;
use App\Models\Experiences\Experiences;
use App\Models\CompanyProviders\CompanyProviders;
use App\Models\ProviderTypes\ProviderTypes;
use App\Models\Credentials\Credentials;
use App\Models\Schedules\Schedules;

trait Relationship
{
	/**
	 * Belongs To relations with Company.
	 *
	 * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
	 */
	public function user()
	{
	    return $this->belongsTo(User::class, 'user_id');
	}

	/**
     * @return mixed
     */
    public function services()
    {
        return $this->hasMany(ProviderServices::class, 'provider_id');
    }

    /**
     * @return mixed
     */
    public function company()
    {
        return $this->belongsTo(Companies::class, 'current_company');
    }

    public function leavelOfExperience()
    {
    	return $this->belongsTo(Experiences::class, 'level_of_experience');
    }

    public function companies()
    {
        return $this->hasMany(CompanyProviders::class, 'provider_id');
    }

    public function all_companies()
    {
        return $this->hasMany(CompanyProviders::class, 'provider_id');
    }

    public function credentials()
    {
        return $this->hasMany(Credentials::class, 'provider_id');
    }

    public function provider_type()
    {
        return $this->belongsTo(ProviderTypes::class, 'provider_type_id');
    }

    public function schedules()
    {
        return $this->hasMany(Schedules::class, 'provider_id');
    }
}