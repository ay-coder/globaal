<?php namespace App\Models\Providers\Traits\Relationship;

use App\Models\ProviderServices\ProviderServices;

trait Relationship
{
	/**
     * @return mixed
     */
    public function services()
    {
        return $this->hasMany(ProviderServices::class, 'provider_id');
    }
}