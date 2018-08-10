<?php
namespace App\Http\Transformers;

use App\Http\Transformers;
use URL;

class TestimonialsTransformer extends Transformer
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
            "testimonialsId" => (int) $item->id, "testimonialsUserId" =>  $item->user_id, "testimonialsProviderId" =>  $item->provider_id, "testimonialsServiceId" =>  $item->service_id, "testimonialsTitle" =>  $item->title, "testimonialsDescription" =>  $item->description, "testimonialsCreatedAt" =>  $item->created_at, "testimonialsUpdatedAt" =>  $item->updated_at, 
        ];
    }

    public function singleTestimonialTransform($item)
    {
        $response = [];

        if(isset($item))
        {
            $response = [
                'user_id'       => (int) $item->user_id,
                'name'          => $item->user->name,
                'email'         => $item->user->email,
                'provider_id'   => (int) $item->provider_id,
                'provider_name' => isset($item->provider) ? $item->provider->name : '',
                'service_id'    => (int) $item->service_id,
                'service_title' => isset($item->service) ? $item->service->title : '',
                'company_id'    => (int) $item->company_id,
                'company_name'  => isset($item->company) ? $item->company->company_name :'',
                'title'         => $item->title,
                'description'         => $item->description,
                'before_image'  =>  URL::to('/').'/uploads/testimonials/'.$item->before_image,
                'after_image'   =>  URL::to('/').'/uploads/testimonials/'.$item->after_image,
            ];
        }

        return $response;
    }

    /**
     * Transform Company Testimonials
     * 
     * @param array $items
     * @return array
     */
    public function transformCompanyTestimonials($items)
    {
        $response = [];
        
        if(isset($items) && count($items))       
        {
            foreach($items as $item)
            {
                $response[] = [
                    'user_id'       => (int) $item->user_id,
                    'name'          => $item->user->name,
                    'email'         => $item->user->email,
                    'provider_id'   => 0,
                    'provider_name' => '',
                    'service_id'    => (int) $item->service_id,
                    'service_title' => isset($item->service) ? $item->service->title : '',
                    'company_id'    => (int) $item->company_id,
                    'company_name'  => isset($item->company) ? $item->company->company_name :'',
                    'title'         => $item->title,
                    'description'         => $item->description,
                    'before_image'  =>  URL::to('/').'/uploads/testimonials/'.$item->before_image,
                    'after_image'   =>  URL::to('/').'/uploads/testimonials/'.$item->after_image,
                ];
            }

        }

        return $response;
    }

    /**
     * Transform Provider Testimonials
     * 
     * @param array $items
     * @return array
     */
    public function transformProviderTestimonials($items)
    {
        $response = [];
        
        if(isset($items) && count($items))       
        {
            foreach($items as $item)
            {
                $response[] = [
                    'user_id'       => (int) $item->user_id,
                    'name'          => $item->user->name,
                    'email'         => $item->user->email,
                    'provider_id'   => $item->provider_id,
                    'provider_name' => $item->provider->name,
                    'service_id'    => (int) $item->service_id,
                    'service_title' => isset($item->service) ? $item->service->title : '',
                    'company_id'    => 0,
                    'company_name'  => '',
                    'title'         => $item->title,
                    'description'         => $item->description,
                    'before_image'  =>  URL::to('/').'/uploads/testimonials/'.$item->before_image,
                    'after_image'   =>  URL::to('/').'/uploads/testimonials/'.$item->after_image,
                ];
            }

        }

        return $response;
    }
}