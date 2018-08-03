<?php namespace App\Models\Services\Traits\Relationship;

use App\Models\MasterCategories\MasterCategories;

trait Relationship
{

	/**
     * @return mixed
     */
    public function master_category()
    {
        return $this->belongsTo(MasterCategories::class);
    }
}