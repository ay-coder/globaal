<?php
namespace App\Http\Transformers;

use App\Http\Transformers;

class ServicesTransformer extends Transformer
{
    /**
     * Transform
     *
     * @param array $data
     * @return array
     */
    public function transform($item)
    {
        if(is_array($item))
        {
            $item = (object)$item;
        }

        return [
            "servicesId" => (int) $item->id, "servicesCategoryId" =>  $item->category_id, "servicesTitle" =>  $item->title, "servicesDescription" =>  $item->description, "servicesCreatedAt" =>  $item->created_at, "servicesUpdatedAt" =>  $item->updated_at, 
        ];
    }
}