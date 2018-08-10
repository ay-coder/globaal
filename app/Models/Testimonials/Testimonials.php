<?php namespace App\Models\Testimonials;

/**
 * Class Testimonials
 *
 * @author Anuj Jaha ( er.anujjaha@gmail.com)
 */

use App\Models\BaseModel;
use App\Models\Testimonials\Traits\Attribute\Attribute;
use App\Models\Testimonials\Traits\Relationship\Relationship;

class Testimonials extends BaseModel
{
    use Attribute, Relationship;
    /**
     * Database Table
     *
     */
    protected $table = "data_testimonials";

    /**
     * Fillable Database Fields
     *
     */
    protected $fillable = [
        "id", "user_id", "provider_id", "service_id", "title", 
        "company_id", 'before_image', 'after_image',
        "description", "created_at", "updated_at", 
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