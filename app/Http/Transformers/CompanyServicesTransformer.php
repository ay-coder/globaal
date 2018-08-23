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

    /**
     * Transform Company WithServices
     * 
     * @param object $company 
     * @return array
     */
    public function transformCompanyWithServices($company, $services)
    {
        $response = [];

        if(isset($company))
        {
            if(isset($company->company_services) && count($company->company_services))   
            {
                foreach($company->company_services as $service)
                {
                    if(isset($services[$service->service_id]))
                    {
                        $response[] = [
                            'id'    => (int) $service->service_id,
                            'title' => $services[$service->service_id]
                        ];
                    }
                }
            }
        }

        return $response;
    }

     /**
     * Transform Company WithServices
     * 
     * @param object $company 
     * @return array
     */
    public function transformSearchCompanyWithServices($companyServices, $services)
    {
        $response = [];

        if(isset($services))
        {
            foreach($services as $service)
            {
                if(! in_array($service->id, $companyServices))
                {
                    $response[] = [
                        'id'    => (int) $service->id,
                        'title' => $service->title
                    ];
                }
            }
        }

        return $response;
    }
}