<?php namespace App\Models\Providers;

/**
 * Class Providers
 *
 * @author Anuj Jaha ( er.anujjaha@gmail.com)
 */

use App\Models\BaseModel;
use App\Models\Providers\Traits\Attribute\Attribute;
use App\Models\Providers\Traits\Relationship\Relationship;

class Providers extends BaseModel
{
    use Attribute, Relationship;
    /**
     * Database Table
     *
     */
    protected $table = "data_providers";

    /**
     * Fillable Database Fields
     *
     */
    protected $fillable = [
        "id", "user_id", "level_of_experience", "current_company", "created_at", "updated_at", 
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