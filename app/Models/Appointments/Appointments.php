<?php namespace App\Models\Appointments;

/**
 * Class Appointments
 *
 * @author Anuj Jaha ( er.anujjaha@gmail.com)
 */

use App\Models\BaseModel;
use App\Models\Appointments\Traits\Attribute\Attribute;
use App\Models\Appointments\Traits\Relationship\Relationship;

class Appointments extends BaseModel
{
    use Attribute, Relationship;
    /**
     * Database Table
     *
     */
    protected $table = "data_appointments";

    /**
     * Fillable Database Fields
     *
     */
    protected $fillable = [
        "id", "user_id", "provider_id", "service_id", "company_id", "booking_date", "start_time", "end_time", "current_status", "status", "created_at", "updated_at", 
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