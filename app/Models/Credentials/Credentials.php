<?php namespace App\Models\Credentials;

/**
 * Class Credentials
 *
 * @author Anuj Jaha ( er.anujjaha@gmail.com)
 */

use App\Models\BaseModel;
use App\Models\Credentials\Traits\Attribute\Attribute;
use App\Models\Credentials\Traits\Relationship\Relationship;

class Credentials extends BaseModel
{
    use Attribute, Relationship;
    /**
     * Database Table
     *
     */
    protected $table = "data_credentials";

    /**
     * Fillable Database Fields
     *
     */
    protected $fillable = [
        "id", "provider_id", "image", "title", "description", "status", "created_at", "updated_at", 
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