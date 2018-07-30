<?php namespace App\Models\Companies;

/**
 * Class Companies
 *
 * @author Anuj Jaha ( er.anujjaha@gmail.com)
 */

use App\Models\BaseModel;
use App\Models\Companies\Traits\Attribute\Attribute;
use App\Models\Companies\Traits\Relationship\Relationship;

class Companies extends BaseModel
{
    use Attribute, Relationship;
    /**
     * Database Table
     *
     */
    protected $table = "data_companies";

    /**
     * Fillable Database Fields
     *
     */
    protected $fillable = [
        "id", "user_id", "company_name", "start_time", "end_time", "created_at", "updated_at", 
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