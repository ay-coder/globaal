<?php
namespace App\Http\Transformers;

use App\Http\Transformers;

class CompanyProvidersTransformer extends Transformer
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
            "companyprovidersId" => (int) $item->id, "companyprovidersProviderId" =>  $item->provider_id, "companyprovidersCompanyId" =>  $item->company_id, "companyprovidersCreatedAt" =>  $item->created_at, "companyprovidersUpdatedAt" =>  $item->updated_at, 
        ];
    }
}