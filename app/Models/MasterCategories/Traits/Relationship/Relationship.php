<?php namespace App\Models\MasterCategories\Traits\Relationship;

use App\Models\Services\Services;

trait Relationship
{
	/**
     * @return mixed
     */
    public function services()
    {
        return $this->hasMany(Services::class, 'category_id');
    }
}