<?php namespace App\Models\ProviderServices\Traits\Relationship;

use App\Models\Access\User\User;
use App\Models\Services\Services;

trait Relationship
{
	/**
     * Many-to-Many relations with Role.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function provider()
    {
        return $this->belongsTo(User::class, 'provider_id');
    }

    /**
     * Many-to-Many relations with Role.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function service()
    {
        return $this->belongsTo(Services::class, 'service_id');
    }
}