<?php namespace App\Models\ProviderTypes;

/**
 * Class ProviderTypes
 *
 * @author Anuj Jaha ( er.anujjaha@gmail.com)
 */

use App\Models\BaseModel;
use App\Models\ProviderTypes\Traits\Attribute\Attribute;
use App\Models\ProviderTypes\Traits\Relationship\Relationship;

class ProviderTypes extends BaseModel
{
    use Attribute, Relationship;
    /**
     * Database Table
     *
     */
    protected $table = "data_provider_types";

    /**
     * Fillable Database Fields
     *
     */
    protected $fillable = [
        "id", "title", "created_at", "updated_at", 
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