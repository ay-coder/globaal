<?php
namespace App\Http\Transformers;

use App\Http\Transformers;
use URL;

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

    public function transformProviders($items)
    {
        $response = [];

        if($items)
        {
            foreach($items as $item)
            {
                $allServices = [];

                if(isset($item->services) && count($item->services))
                {
                    foreach($item->services as $service)
                    {
                        $allServices[] = [
                            'service_id' => (int) $service->service_id,
                            'title'      => isset($service->service) ? $service->service->title : ''
                        ];
                    }
                }

                $response[] = [
                    'provider_id'   => (int) $item->id,
                    'name'          => $item->user->name,
                    'email'         => $item->user->email,
                    'company_id'    => (int) $item->company->id,
                    'company_name'  => $this->nulltoBlank($item->company->company_name),
                    'profile_pic'   => URL::to('/').'/uploads/user/' . $item->user->profile_pic, 
                    'level_of_experience'   => $item->leavelOfExperience->level_of_experience,
                    'services'      => $allServices

                ];
            }
        }

        return $response;
    }

    public function transformSingleProviders($item)
    {
        $response = [];

        if($item)
        {
            $allServices = [];

            if(isset($item->services) && count($item->services))
            {
                foreach($item->services as $service)
                {
                    $allServices[] = [
                        'service_id' => (int) $service->service_id,
                        'title'      => isset($service->service) ? $service->service->title : ''
                    ];
                }
            }

            $response[] = [
                'provider_id'   => (int) $item->id,
                'name'          => $item->user->name,
                'email'         => $item->user->email,
                'company_id'    => (int) $item->company->id,
                'company_name'  => $this->nulltoBlank($item->company->company_name),
                'profile_pic'   => URL::to('/').'/uploads/user/' . $item->user->profile_pic, 
                'level_of_experience'   => $item->leavelOfExperience->level_of_experience,
                'services'      => $allServices
            ];
        }

        return $response;
    }
}