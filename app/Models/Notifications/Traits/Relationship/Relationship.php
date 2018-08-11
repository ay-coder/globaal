<?php namespace App\Models\Notifications\Traits\Relationship;

use App\Models\Access\User\User;
use App\Models\Providers\Providers;
use App\Models\Companies\Companies;
use App\Models\Services\Services;

trait Relationship
{
	/**
     * @return mixed
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

	/**
     * @return mixed
     */
    public function patient()
    {
        return $this->belongsTo(User::class, 'patient_id');
    } 

    /**
     * @return mixed
     */
    public function provider()
    {
    	return $this->belongsTo(Providers::class, 'user_id');
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
    public function service()
    {
    	return $this->belongsTo(Services::class, 'service_id');
    } 
}