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
                $isSchedule     = 0;

                if(isset($item->schedules) && count($item->schedules))
                {
                    if(isset($item->company))
                    {
                        $isExist = $item->schedules->where('company_id', $item->company->id)->first();

                        if(isset($isExist))
                        {
                            $isSchedule = 1;
                        }
                    }
                    else
                    {
                       $isSchedule = 1;
                    }
                }

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
                            'image'             => isset($credential->image) ? URL::to('/').'/uploads/credentials/'.$credential->image : '',
                            'title'             => $credential->title
                        ];
                    }
                }

                $response[] = [
                    'provider_id'   => (int) $item->id,
                    'name'          => $item->user->name,
                    'email'         => $item->user->email,
                    'company_id'    => isset($item->company) ? (int) $item->company->id : '',
                    'company_name'  => isset($item->company) ? $this->nulltoBlank($item->company->company_name) : '',
                    'company_lat'   => isset($item->company->user) ? (float)  $item->company->user->lat : 0,
                    'company_long'   => isset($item->company->user) ? (float)  $item->company->user->long : 0,
                    'profile_pic'   => URL::to('/').'/uploads/user/' . $item->user->profile_pic, 
                    'is_schedule'   => $isSchedule,
                    'level_of_experience'   => isset($item->leavelOfExperience) ? $item->leavelOfExperience->level_of_experience : '',
                    'services'      => $allServices,
                    'companies'     => $allCompanies,
                    'credentials'   => $allCredentials

                ];
            }
        }

        return $response;
    }

    public function transformSingleProviders($item, $testimonials = array())
    {
        $response = [];

        if($item)
        {
            $allServices    = [];
            $allCompanies   = [];
            $allCredentials = [];
            $allTestimonials = [];

            if(isset($testimonials) && count($testimonials))
            {
                foreach($testimonials as $testimonial)
                {
                    $allTestimonials[] = [
                        'service_id'    => (int) isset($testimonial->service_id) ? $testimonial->service_id : 0,
                        'service_title' => isset($testimonial->service) ? $testimonial->service->title : '',
                        'title'         => $testimonial->title,
                        'description'         => $testimonial->description,
                        'before_image'  =>  URL::to('/').'/uploads/testimonials/'.$testimonial->before_image,
                        'after_image'   =>  URL::to('/').'/uploads/testimonials/'.$testimonial->after_image,
                    ];
                }
            }

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
                    $isConnected = $company->accept_by_provider == 1 && $company->accept_by_company == 1 ? 1 : 0;

                    if($company->accept_by_provider == 1)
                    {
                        $isGenerated = 'Provider';
                    }

                    if($company->accept_by_company == 1)
                    {
                        $isGenerated = 'Company';
                    }

                    if($isConnected == 1)
                    {
                        $isGenerated = '';
                    }

                    $allCompanies[] = [
                        'company_id'    => (int) $company->company_id,
                        'is_connected'  => $isConnected,
                        'is_generated'  => $isGenerated,
                        'company_name'  => isset($company->company->company_name) ? $company->company->company_name : '',
                        'profile_pic'   => URL::to('/').'/uploads/user/' . $company->company->user->profile_pic, 
                    ];
                }
            }

            if(isset($item->credentials) && count($item->credentials))
            {
                foreach($item->credentials as $credential)
                {
                    $allCredentials[] = [
                        'credential_id'     => (int) $credential->id,
                        'image'             => isset($credential->image) ? URL::to('/').'/uploads/credentials/'.$credential->image : '',
                        'title'             => $credential->title
                    ];
                }
            }

            $response[] = [
                'provider_id'   => (int) $item->id,
                'name'          => $item->user->name,
                'email'         => $item->user->email,
                'mobile'         => $this->nulltoBlank($item->user->mobile),
                'address'       => $this->nulltoBlank($item->user->address),
                'company_id'    => (int) isset($item->company) ? $item->company->id : 0,
                'company_name'  => isset($item->company) ? $this->nulltoBlank($item->company->company_name) : '',
                'profile_pic'   => URL::to('/').'/uploads/user/' . $item->user->profile_pic, 
                'level_of_experience'   => isset($item->leavelOfExperience) ? $item->leavelOfExperience->level_of_experience : '',
                'services'      => $allServices,
                'companies'     => $allCompanies,
                'credentials'   => $allCredentials,
                'testimonials'  => $allTestimonials
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
                if(isset($item->provider->user))
                {
                    $response[] = [
                        'request_id'    => (int) $item->id,
                        'provider_id'   => (int) $item->provider_id,
                        'company_id'    => (int) $item->company_id,
                        'company_name'  => $item->company->company_name,
                        'company_image' =>  URL::to('/').'/uploads/user/' . $item->company->user->profile_pic,
                        'provider_name' => $item->provider->user->name,
                        'provider_image' =>  URL::to('/').'/uploads/user/' . $item->provider->user->profile_pic,
                        'created_at'    => date('Y-m-d H:i:s', strtotime($item->created_at))
                    ];
                }
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
        $response   = [];
        $userId     = access()->user()->id;
        $providerId = access()->getProviderId($userId);
        $companyIds = access()->getProviderCompanies($providerId);

        if(isset($items) && count($items))
        {
            foreach($items as $item)
            {
                $isConnected = in_array($item->id, $companyIds) ? 1 : 0;

                $response[] = [
                    'company_id'    => (int) $item->id,
                    'is_connected'  => $isConnected,
                    'company_name'  => $item->company_name,
                    'profile_pic'   => URL::to('/').'/uploads/user/' . $item->user->profile_pic
                ];
            }
        }
        return $response;
    }
}