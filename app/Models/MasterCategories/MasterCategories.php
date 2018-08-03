<?php namespace App\Models\MasterCategories;

/**
 * Class MasterCategories
 *
 * @author Anuj Jaha ( er.anujjaha@gmail.com)
 */

use App\Models\BaseModel;
use App\Models\MasterCategories\Traits\Attribute\Attribute;
use App\Models\MasterCategories\Traits\Relationship\Relationship;

class MasterCategories extends BaseModel
{
    use Attribute, Relationship;
    /**
     * Database Table
     *
     */
    protected $table = "data_master_categories";

    /**
     * Fillable Database Fields
     *
     */
    protected $fillable = [
        "id", "title", "description", "created_at", "updated_at", 
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