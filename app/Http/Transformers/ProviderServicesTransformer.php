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

    /**
     * Transform Provider With Services
     * 
     * @param object $provider
     * @param array $services
     * @return array
     */
    public function transformProviderWithServices($provider, $services = array())
    {
        $response = [];

        if(isset($provider))
        {
            if(isset($provider->services))
            {
                foreach($provider->services as $service)   
                {
                    $response[] = [
                        'service_id' => (int) $service->service_id,
                        'title'      => $this->nulltoBlank($service->service->title)
                    ];
                }
            }
        }

        return $response;
    }

    /**
     * Transform Provider Search Services
     * 
     * @param object $provider
     * @param array  $services
     * @return array
     */
    public function transformProviderSearchServices($serviceIds, $services = array())
    {
        $response = [];

        foreach($services as $service)   
        {
            if(! in_array($service->id, $serviceIds))
            {
                $response[] = [
                    'service_id' => (int) $service->id,
                    'title'      => $service->title
                ];
            }
        }

        return $response;
    }

}