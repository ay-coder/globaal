<?php namespace App\Models\Testimonials\Traits\Relationship;

use App\Models\Access\User\User;
use App\Models\Companies\Companies;
use App\Models\Services\Services;

trait Relationship
{
	/**
     * Belongs To relations with User
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Belongs To relations with Company
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function company()
    {
        return $this->belongsTo(Companies::class, 'company_id');
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

    /**
     * Belongs To relations with Service
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function service()
    {
        return $this->belongsTo(Services::class, 'service_id');
    }
}