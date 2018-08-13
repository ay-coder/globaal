<?php namespace App\Models\Schedules;

/**
 * Class Schedules
 *
 * @author Anuj Jaha ( er.anujjaha@gmail.com)
 */

use App\Models\BaseModel;
use App\Models\Schedules\Traits\Attribute\Attribute;
use App\Models\Schedules\Traits\Relationship\Relationship;

class Schedules extends BaseModel
{
    use Attribute, Relationship;
    /**
     * Database Table
     *
     */
    protected $table = "data_schedules";

    /**
     * Fillable Database Fields
     *
     */
    protected $fillable = [
        "id", "user_id", "provider_id", "service_id", "company_id", "day_name", "start_time", "end_time", "status", "created_at", "updated_at", 
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