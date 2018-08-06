<?php
namespace App\Http\Transformers;

use App\Http\Transformers;

class ProviderServicesTransformer extends Transformer
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
            "providerservicesId" => (int) $item->id, "providerservicesProviderId" =>  $item->provider_id, "providerservicesServiceId" =>  $item->service_id, "providerservicesCreatedAt" =>  $item->created_at, "providerservicesUpdatedAt" =>  $item->updated_at, 
        ];
    }
}