<?php
namespace App\Http\Transformers;

use App\Http\Transformers;
use URL;

class CompaniesTransformer extends Transformer
{

    public function transform($item)
    {
        return $item;
    }

    /**
     * Transform
     *
     * @param array $data
     * @return array
     */
    public function companyTranform($items)
    {
        $response = [];

        if(isset($items) && count($items))
        {
            foreach($items as $item)
            {
                $item = (object)$item;
                
                $response[] = [
                    "company_id"    => (int) $item->id,
                    "company_name"  =>  $this->nulltoBlank($item->company_name),
                    "start_time"    =>  $this->nulltoBlank($item->start_time),
                    "end_time"      =>  $this->nulltoBlank($item->end_time)
                ];
            }
        }

        return $response;
    }

    public function singleCompanyTransform($companyInfo)
    {
        $response       = [];
        $providers      = [];
        $testimonials   = [];
        $services       = [];

        if(isset($companyInfo->company_all_providers))
        {
            foreach($companyInfo->company_all_providers as $provider)
            {
                $isConnected = $provider->accept_by_provider == 1 && $provider->accept_by_company == 1 ? 1 : 0;

                $providers[] = [
                    'provider_id'           => (int) $provider->provider_id,
                    'is_connected'          => $isConnected,
                    'name'                  => $provider->provider->user->name,
                    'profile_pic'           => URL::to('/').'/uploads/user/' . $provider->provider->user->profile_pic, 
                ];
            }
        }

        if(isset($companyInfo->company_services))
        {
            foreach($companyInfo->company_services as $service)
            {
                $services[] = [
                    'service_id'    => (int) $service->service->id,
                    'title'         => $service->service->title
                ];
            }
        }

        if(isset($companyInfo->company_testimonials))
        {
            foreach($companyInfo->company_testimonials as $testimonial)
            {
                $testimonials[] = [
                    'testimonial_id'=> (int) $testimonial->id,
                    'title'         => $testimonial->title,
                    'description'   => $this->nulltoBlank($testimonial->description),
                    'before_image'  =>  URL::to('/').'/uploads/testimonials/'.$testimonial->before_image,
                    'after_image'   =>  URL::to('/').'/uploads/testimonials/'.$testimonial->after_image,
                ];
            }
        }

           
            $response = [
                "company_id"    => (int) $companyInfo->id,
                "company_name"  =>  $this->nulltoBlank($companyInfo->company_name),
                "start_time"    =>  $this->nulltoBlank($companyInfo->start_time),
                "end_time"      =>  $this->nulltoBlank($companyInfo->end_time),
                'address'       => $this->nulltoBlank($companyInfo->user->address),
                'mobile'         => $this->nulltoBlank($companyInfo->user->mobile),
                'profile_pic'   => URL::to('/').'/uploads/user/' . $companyInfo->user->profile_pic, 
                'address'       => $this->nulltoBlank($companyInfo->user->address),
                'city'                  => $this->nulltoBlank($companyInfo->user->city),
                'state'                 => $this->nulltoBlank($companyInfo->user->state),
                'zip'                   => $this->nulltoBlank($companyInfo->user->zip),
                'gender'                => $this->nulltoBlank($companyInfo->user->gender),
                'providers'     => $providers,
                'testimonials'  => $testimonials,
                'services'      => $services
            ];
        
        return $response; 
    }

    public function companyTranformSearchProviders($items)
    {
        $response       = [];
        $userId         = access()->user()->id;
        $companyId      = access()->getProviderId($userId);
        $providerIds    = access()->getProviderCompanies($companyId);

        if(isset($items))
        {
            foreach($items as $item)
            {
                $isConnected    = in_array($item->id, $providerIds) ? 1 :0;
                $response[]     = [
                    'provider_id'           => (int) $item->id,
                    'name'                  => $item->user->name,
                    'is_connected'          => $isConnected,
                    'level_of_experience'   => $item->level_of_experience,
                    'profile_pic'           => URL::to('/').'/uploads/user/' . $item->user->profile_pic

                ];
            }
        }

        return $response;
    }

    public function companyTranformWithProviders($items)
    {
        $response = [];

        if(isset($items) && count($items))
        {
            foreach($items as $item)
            {
                $providers  = [];
                $item       = (object)$item;

                if(isset($item->company_providers) && count($item->company_providers))
                {
                    foreach($item->company_providers as $provider)
                    {
                        $providers[] = [
                            'provider_id'           => $provider->provider_id,
                            'name'                  => $provider->provider->name,
                            'profile_pic'           => URL::to('/').'/uploads/user/' . $provider->provider->profile_pic, 
                        ];
                    }
                }
                
                $response[] = [
                    "company_id"    => (int) $item->id,
                    "company_name"  =>  $this->nulltoBlank($item->company_name),
                    "start_time"    =>  $this->nulltoBlank($item->start_time),
                    "end_time"      =>  $this->nulltoBlank($item->end_time),
                    'providers'     => $providers
                ];
            }
        }

        return $response; 
    }

    public function singleCompanyTranformWithProviders($items)
    {
        $response = [];

       
        if(isset($item->company_providers) && count($item->company_providers))
        {
            foreach($item->company_providers as $provider)
            {
                $providers[] = [
                    'provider_id'           => $provider->provider_id,
                    'name'                  => $provider->provider->name,
                    'profile_pic'           => URL::to('/').'/uploads/user/' . $provider->provider->profile_pic, 
                ];
            }
        }
                
        return [
            "company_id"    => (int) $item->id,
            "company_name"  =>  $this->nulltoBlank($item->company_name),
            "start_time"    =>  $this->nulltoBlank($item->start_time),
            "end_time"      =>  $this->nulltoBlank($item->end_time),
            'providers'     => $providers
        ];
    }

    public function companyTranformWithDistance($companies, $allCompanies = array())
    {
        $response = [];

        if(isset($companies))   
        {
            foreach($companies as $item)
            {
                $company = $allCompanies->where('user_id', $item->id)->first();
                if(isset($company->id))
                {
                    $response[] = [
                        "company_id"    => (int) $item->id,
                        "company_name"  =>  $company->company_name,
                        "start_time"    =>  $company->start_time,
                        "end_time"      =>  $company->end_time,
                        'distance'      => (float) number_format($item->distance, 3)
                    ];
                }
            }
        }

        return $response;
    }
}