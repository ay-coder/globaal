<?php namespace App\Models\CompanyProviders\Traits\Relationship;

use App\Models\Access\User\User;
use App\Models\Companies\Companies;
use App\Models\Providers\Providers;

trait Relationship
{
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
        return $this->belongsTo(Providers::class, 'provider_id');
    }
}