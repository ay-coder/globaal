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
}