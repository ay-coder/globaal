<?php namespace App\Models\CompanyProviders;

/**
 * Class CompanyProviders
 *
 * @author Anuj Jaha ( er.anujjaha@gmail.com)
 */

use App\Models\BaseModel;
use App\Models\CompanyProviders\Traits\Attribute\Attribute;
use App\Models\CompanyProviders\Traits\Relationship\Relationship;

class CompanyProviders extends BaseModel
{
    use Attribute, Relationship;
    /**
     * Database Table
     *
     */
    protected $table = "data_company_providers";

    /**
     * Fillable Database Fields
     *
     */
    protected $fillable = [
        "id", "provider_id", "company_id", 
        "accept_by_provider", "accept_by_company",
        "created_at", "updated_at", 
    ];

    /**
     * Timestamp flag
     *
     */
    public $timestamps = true;

    /**
     * Guarded ID Column
     *
     */
    protected $guarded = ["id"];
}