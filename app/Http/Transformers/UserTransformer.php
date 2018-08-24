<?php

namespace App\Http\Transformers;

use App\Http\Transformers;
use URL;

class UserTransformer extends Transformer 
{
    public function transform($data) 
    {
        return [
            'user_id'               => (int) $data->id,
            'user_token'            => $this->nulltoBlank($data->token),
            'email'                 => $this->nulltoBlank($data->email),
            'user_type'             => isset($data->user_type) ? (int) $data->user_type : 0,
            'name'                  => $this->nulltoBlank($data->name),
            'mobile'                => $this->nulltoBlank($data->mobile),
            'device_token'          => $this->nulltoBlank($data->device_token),
            'device_type'           => isset($data->device_type) ? (int) $data->device_type : 0,
            'profile_pic'           => URL::to('/').'/uploads/user/' . $data->profile_pic, 
            'address'               => $this->nulltoBlank($data->address),
            'city'                  => $this->nulltoBlank($data->city),
            'state'                 => $this->nulltoBlank($data->state),
            'zip'                   => $this->nulltoBlank($data->zip),
            'gender'                => $this->nulltoBlank($data->gender),
            'birthdate'             => $this->nulltoBlank($data->birthdate),
            'notification_count'    => (int) access()->getUserUnreadNotificationCount($data->id),
            'status'                => $data->status,
            'social_provider'       => $this->nulltoBlank($data->social_provider),
            'social_token'          => $this->nulltoBlank($data->social_token)
        ];
    }

    public function companyTranform($data) 
    {
        return [
            'user_id'               => (int) $data->id,
            'user_token'            => $this->nulltoBlank($data->token),
            'email'                 => $this->nulltoBlank($data->email),
            'user_type'             => isset($data->user_type) ? (int) $data->user_type : 0,
            'name'                  => $this->nulltoBlank($data->name),
            'mobile'                => $this->nulltoBlank($data->mobile),
            'device_token'          => $this->nulltoBlank($data->device_token),
            'device_type'           => isset($data->device_type) ? (int) $data->device_type : 0,
            'profile_pic'           => URL::to('/').'/uploads/user/' . $data->profile_pic, 
            'address'               => $this->nulltoBlank($data->address),
            'city'                  => $this->nulltoBlank($data->city),
            'state'                 => $this->nulltoBlank($data->state),
            'zip'                   => $this->nulltoBlank($data->zip),
            'gender'                => $this->nulltoBlank($data->gender),
            'birthdate'             => $this->nulltoBlank($data->birthdate),
            'notification_count'    => (int) access()->getUserUnreadNotificationCount($data->id),
            'status'                => $data->status,
            'social_provider'       => $this->nulltoBlank($data->social_provider),
            'social_token'          => $this->nulltoBlank($data->social_token),
            'company_name'          => $this->nulltoBlank($data->company_name),
            'start_time'            => $this->nulltoBlank($data->start_time),
            'end_time'              => $this->nulltoBlank($data->end_time)
        ];
    }

    public function providerTranform($data, $services, $companies, $myProvider, $allServices) 
    {
        $providerServices   = [];
        $providerCompanies  = [];

        if(isset($services) && count($services))
        {
            foreach($services as $service)
            {
                $myService = $allServices->where('id', $service->service_id)->first();

                if(isset($myService))
                {
                    $providerServices[] = [
                        'service_id'            => (int) $service->service_id,
                        'service_category_id'   => (int) $myService->category_id,
                        'title'                 => $myService->title
                    ];
                }

            }
        }

        if(isset($companies) && count($companies))
        {
            foreach($companies as $company)
            {
                if(isset($company->company))
                {
                    $providerCompanies[] = [
                        'company_id'        => (int) $company->company->id,
                        'company_name'      => $company->company->company_name
                    ];
                }
            }
        }

        return [
            'user_id'               => (int) $data->id,
            'user_token'            => $this->nulltoBlank($data->token),
            'email'                 => $this->nulltoBlank($data->email),
            'user_type'             => isset($data->user_type) ? (int) $data->user_type : 0,
            'name'                  => $this->nulltoBlank($data->name),
            'mobile'                => $this->nulltoBlank($data->mobile),
            'device_token'          => $this->nulltoBlank($data->device_token),
            'device_type'           => isset($data->device_type) ? (int) $data->device_type : 0,
            'profile_pic'           => URL::to('/').'/uploads/user/' . $data->profile_pic, 
            'address'               => $this->nulltoBlank($data->address),
            'city'                  => $this->nulltoBlank($data->city),
            'state'                 => $this->nulltoBlank($data->state),
            'zip'                   => $this->nulltoBlank($data->zip),
            'gender'                => $this->nulltoBlank($data->gender),
            'birthdate'             => $this->nulltoBlank($data->birthdate),
            'notification_count'    => (int) access()->getUserUnreadNotificationCount($data->id),
            'status'                => $data->status,
            'social_provider'       => $this->nulltoBlank($data->social_provider),
            'social_token'          => $this->nulltoBlank($data->social_token),
            'level_of_experience'   => $this->nulltoBlank($data->level_of_experience),
            'current_company'       => $this->nulltoBlank($data->current_company),
            'provider_type'         => isset($myProvider->provider_type->title) ? $myProvider->provider_type->title : '',
            'services'              => $providerServices,
            'companies'             => $providerCompanies
        ];
    }
    
    /**
     * Update User
     * 
     * @param object $data
     * @return array
     */
    public function updateUser($data)
    {
        $headerToken = request()->header('Authorization');
        $userToken   = '';

        if($headerToken)
        {
            $token      = explode(" ", $headerToken);
            $userToken  = $token[1];
        }

        return [
            'user_id'            => (int) $data->id,
            'user_token'         => $userToken,
            'user_type'          => isset($data->user_type) ? (int) $data->user_type : 0,
            'name'               => $this->nulltoBlank($data->name),
            'email'              => $this->nulltoBlank($data->email),
            'mobile'             => $this->nulltoBlank($data->mobile),
            'device_token'       => $this->nulltoBlank($data->device_token),
            'device_type'        => isset($data->device_type) ? (int) $data->device_type : 0,
            'profile_pic'        => URL::to('/').'/uploads/user/' . $data->profile_pic, 
            'address'            => $this->nulltoBlank($data->address),
            'city'               => $this->nulltoBlank($data->city),
            'state'              => $this->nulltoBlank($data->state),
            'zip'                => $this->nulltoBlank($data->zip),
            'gender'             => $this->nulltoBlank($data->gender),
            'birthdate'          => $this->nulltoBlank($data->birthdate),
            'notification_count' => (int) access()->getUserUnreadNotificationCount($data->id),
            'status'             => $data->status,
            'social_provider'    => $this->nulltoBlank($data->social_provider),
            'social_token'       => $this->nulltoBlank($data->social_token)
        ];  
    }

    public function getUserInfo($data) 
    {
        return [
            'userId'    => $data->id,
            'name'      => $this->nulltoBlank($data->name),
            'email'     => $this->nulltoBlank($data->email)
        ];
    }
    
    /**
     * userDetail
     * Single user detail
     * 
     * @param type $data
     * @return type
     */
    public function userDetail($data) {
        return [
            'UserId' => isset($data['id']) ? $data['id'] : "",
            'QuickBlocksId' => isset($data['quick_blocks_id']) ? $data['quick_blocks_id'] : "",
            'MobileNumber' => isset($data['mobile_number']) ? $data['mobile_number'] : "",
            'Name' => isset($data['username']) ? $data['username'] : "",
            'Specialty' => isset($data['specialty']) ? $data['specialty'] : "",
            'ProfilePhoto' => isset($data['profile_photo'])?$this->getUserImage($data['profile_photo']):""
        ];
    }

    /*
     * User Detail and it's parameters
     */
    public function singleUserDetail($data){        
        return [
            'UserId' => $data['id'],            
            'Name' => $this->nulltoBlank($data['name']),
            'Email' => $this->nulltoBlank($data['email']),
            'MobileNumber' => $this->nulltoBlank($data['mobile_number']),
        ];
    }
    
    public function transformStateCollection(array $items) {
        return array_map([$this, 'getState'], $items);
    }
}
