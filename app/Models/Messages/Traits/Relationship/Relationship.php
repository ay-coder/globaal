<?php namespace App\Models\Messages\Traits\Relationship;

use App\Models\Access\User\User;

trait Relationship
{
	/**
     * Belongs To relations with Patient
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function patient()
    {
        return $this->belongsTo(User::class, 'patient_id');
    }

    /**
     * Belongs To relations with Provider
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function provider()
    {
        return $this->belongsTo(User::class, 'provider_id');
    }
}