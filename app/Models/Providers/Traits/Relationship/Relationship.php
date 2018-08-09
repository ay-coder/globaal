<?php namespace App\Models\Providers\Traits\Relationship;

use App\Models\Access\User\User;
use App\Models\ProviderServices\ProviderServices;
use App\Models\Companies\Companies;
use App\Models\Experiences\Experiences;

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
}