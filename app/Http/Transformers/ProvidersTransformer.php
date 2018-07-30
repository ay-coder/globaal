<?php
namespace App\Http\Transformers;

use App\Http\Transformers;

class ProvidersTransformer extends Transformer
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
            "providersId" => (int) $item->id, "providersUserId" =>  $item->user_id, "providersLevelOfExperience" =>  $item->level_of_experience, "providersCurrentCompany" =>  $item->current_company, "providersCreatedAt" =>  $item->created_at, "providersUpdatedAt" =>  $item->updated_at, 
        ];
    }
}