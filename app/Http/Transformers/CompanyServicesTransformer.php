<?php
namespace App\Http\Transformers;

use App\Http\Transformers;

class CompanyServicesTransformer extends Transformer
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
            "companyservicesId" => (int) $item->id, "companyservicesCompanyId" =>  $item->company_id, "companyservicesServiceId" =>  $item->service_id, "companyservicesStatus" =>  $item->status, "companyservicesCreatedAt" =>  $item->created_at, "companyservicesUpdatedAt" =>  $item->updated_at, 
        ];
    }
}