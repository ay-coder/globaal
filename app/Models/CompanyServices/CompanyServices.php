<?php namespace App\Models\CompanyServices;

/**
 * Class CompanyServices
 *
 * @author Anuj Jaha ( er.anujjaha@gmail.com)
 */

use App\Models\BaseModel;
use App\Models\CompanyServices\Traits\Attribute\Attribute;
use App\Models\CompanyServices\Traits\Relationship\Relationship;

class CompanyServices extends BaseModel
{
    use Attribute, Relationship;
    /**
     * Database Table
     *
     */
    protected $table = "data_company_services";

    /**
     * Fillable Database Fields
     *
     */
    protected $fillable = [
        "id", "company_id", "service_id", "status", "created_at", "updated_at", 
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