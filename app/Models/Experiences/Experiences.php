<?php namespace App\Models\Experiences;

/**
 * Class Experiences
 *
 * @author Anuj Jaha ( er.anujjaha@gmail.com)
 */

use App\Models\BaseModel;
use App\Models\Experiences\Traits\Attribute\Attribute;
use App\Models\Experiences\Traits\Relationship\Relationship;

class Experiences extends BaseModel
{
    use Attribute, Relationship;
    /**
     * Database Table
     *
     */
    protected $table = "data_level_of_experiences";

    /**
     * Fillable Database Fields
     *
     */
    protected $fillable = [
        "id", "level_of_experience", "created_at", "updated_at", 
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