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
                $allServices    = [];
                $allCompanies   = [];
                $allCredentials = [];

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

                if(isset($item->companies) && count($item->companies))
                {
                    foreach($item->companies as $company)
                    {
                        $allCompanies[] = [
                            'company_id'    => (int) $company->company_id,
                            'company_name'  => isset($company->company->company_name) ? $company->company->company_name : ''
                        ];
                    }
                }

                if(isset($item->credentials) && count($item->credentials))
                {
                    foreach($item->credentials as $credential)
                    {
                        $allCredentials[] = [
                            'credential_id'     => (int) $credential->id,
                            'title'             => $credential->title
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
                    'services'      => $allServices,
                    'companies'     => $allCompanies,
                    'credentials'   => $allCredentials

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
            $allServices    = [];
            $allCompanies   = [];
            $allCredentials = [];

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

            if(isset($item->companies) && count($item->companies))
            {
                foreach($item->companies as $company)
                {
                    $allCompanies[] = [
                        'company_id'    => (int) $company->company_id,
                        'company_name'  => isset($company->company->company_name) ? $company->company->company_name : ''
                    ];
                }
            }

            if(isset($item->credentials) && count($item->credentials))
            {
                foreach($item->credentials as $credential)
                {
                    $allCredentials[] = [
                        'credential_id'     => (int) $credential->id,
                        'title'             => $credential->title
                    ];
                }
            }


            $response[] = [
                'provider_id'   => (int) $item->id,
                'name'          => $item->user->name,
                'email'         => $item->user->email,
                'company_id'    => (int) isset($item->company) ? $item->company->id : 0,
                'company_name'  => isset($item->company) ? $this->nulltoBlank($item->company->company_name) : '',
                'profile_pic'   => URL::to('/').'/uploads/user/' . $item->user->profile_pic, 
                'level_of_experience'   => isset($item->leavelOfExperience) ? $item->leavelOfExperience->level_of_experience : '',
                'services'      => $allServices,
                'companies'     => $allCompanies,
                'credentials'   => $allCredentials
            ];
        }

        return $response;
    }

    /**
     * Trans CompanyRequests
     * 
     * @param array $items
     * @return array
     */
    public function transCompanyRequests($items)
    {
        $response = [];

        if($items)
        {
            foreach($items as $item)
            {
                $response[] = [
                    'request_id'    => (int) $item->id,
                    'provider_id'   => (int) $item->provider_id,
                    'company_id'    => (int) $item->company_id,
                    'company_name'  => $item->company->company_name,
                    'company_image' =>  URL::to('/').'/uploads/user/' . $item->company->user->profile_pic,
                    'provider_name' => $item->provider->name,
                    'provider_image' =>  URL::to('/').'/uploads/user/' . $item->provider->profile_pic,
                    'created_at'    => date('Y-m-d H:i:s', strtotime($item->created_at))
                ];
            }
        }

        return $response;
    }

    /**
     * Provider Transform Companies
     * 
     * @param array $items
     * @return array
     */
    public function providerTransformCompanies($items)
    {
        $response = [];

        if(isset($items) && count($items))
        {
            foreach($items as $item)
            {
                $response[] = [
                    'company_id'    => (int) $item->id,
                    'company_name'  => $item->company_name,
                    'profile_pic'   => URL::to('/').'/uploads/user/' . $item->user->profile_pic
                ];
            }
        }
        return $response;
    }
}