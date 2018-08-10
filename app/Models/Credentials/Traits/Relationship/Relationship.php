<?php namespace App\Models\Credentials\Traits\Relationship;

use App\Models\Access\User\User;

trait Relationship
{
	/**
     * @return mixed
     */
    public function provider()
    {
        return $this->belongsTo(User::class, 'provider_id');
    }
}