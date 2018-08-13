<?php namespace App\Models\Schedules\Traits\Relationship;

use App\Models\Access\User\User;
use App\Models\Services\Services;
use App\Models\ProviderServices\ProviderServices;
use App\Models\Companies\Companies;
use App\Models\Providers\Providers;

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
    public function service()
    {
        return $this->belongsTo(Services::class, 'service_id');
    }

    /**
     * @return mixed
     */
    public function company()
    {
        return $this->belongsTo(Companies::class, 'company_id');
    }

    /**
     * @return mixed
     */
    public function provider()
    {
        return $this->belongsTo(Providers::class, 'provider_id');
    }
}