<?php namespace App\Models\ProviderServices;

/**
 * Class ProviderServices
 *
 * @author Anuj Jaha ( er.anujjaha@gmail.com)
 */

use App\Models\BaseModel;
use App\Models\ProviderServices\Traits\Attribute\Attribute;
use App\Models\ProviderServices\Traits\Relationship\Relationship;

class ProviderServices extends BaseModel
{
    use Attribute, Relationship;
    /**
     * Database Table
     *
     */
    protected $table = "data_provider_services";

    /**
     * Fillable Database Fields
     *
     */
    protected $fillable = [
        "id", "provider_id", "service_id", "created_at", "updated_at", 
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