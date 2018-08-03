<?php namespace App\Models\Services;

/**
 * Class Services
 *
 * @author Anuj Jaha ( er.anujjaha@gmail.com)
 */

use App\Models\BaseModel;
use App\Models\Services\Traits\Attribute\Attribute;
use App\Models\Services\Traits\Relationship\Relationship;

class Services extends BaseModel
{
    use Attribute, Relationship;
    /**
     * Database Table
     *
     */
    protected $table = "data_services";

    /**
     * Fillable Database Fields
     *
     */
    protected $fillable = [
        "id", "category_id", "title", "description", "created_at", "updated_at", 
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