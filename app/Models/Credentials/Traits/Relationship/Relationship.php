<?php namespace App\Models\Credentials\Traits\Relationship;

use App\Models\Access\User\User;
use App\Models\Providers\Providers;

trait Relationship
{
	/**
     * @return mixed
     */
    public function provider()
    {
        return $this->belongsTo(Providers::class, 'provider_id');
    }
}