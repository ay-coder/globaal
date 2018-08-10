<?php
namespace App\Http\Transformers;

use App\Http\Transformers;

class ProviderTypesTransformer extends Transformer
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
            "providertypesId" => (int) $item->id, "providertypesTitle" =>  $item->title, "providertypesCreatedAt" =>  $item->created_at, "providertypesUpdatedAt" =>  $item->updated_at, 
        ];
    }
}