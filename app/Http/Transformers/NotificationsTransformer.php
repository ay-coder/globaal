<?php
namespace App\Http\Transformers;

use App\Http\Transformers;
use URL;

class NotificationsTransformer extends Transformer
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
            "notificationsId" => (int) $item->id, "notificationsUserId" =>  $item->user_id, "notificationsPatientId" =>  $item->patient_id, "notificationsProviderId" =>  $item->provider_id, "notificationsServiceId" =>  $item->service_id, "notificationsCompanyId" =>  $item->company_id, "notificationsTitle" =>  $item->title, "notificationsDescription" =>  $item->description, "notificationsIsRead" =>  $item->is_read, "notificationsCreatedAt" =>  $item->created_at, "notificationsUpdatedAt" =>  $item->updated_at, 
        ];
    }

    public function transformNotifications($items)
    {
        $response = [];

        if($items)
        {
            foreach($items as $item)
            {
                $response[] = [
                    'notification_id'   => (int) $item->id,
                    'patient_id'        => (int) $item->patient_id,
                    'provider_id'       => (int) $item->provider_id,
                    'company_id'        => (int) $item->company_id,
                    'service_id'        => (int) $item->service_id,
                    'patient_name'      => $item->user->name,
                    'provider_name'     => isset($item->provider) ? $item->provider->user->name : '',
                    'company_name'      => isset($item->company) ? $item->company->company_name : '',
                    'service'           => isset($item->service) ? $item->service->title : '',
                    'title'             => $item->title,
                    'is_read'           => $item->is_read,
                    'icon'              => URL::to('/').'/uploads/notifications/default.png', 
                    'created_at'        => date('Y-m-d H:i:s', strtotime($item->created_at))
                ];
            }
        }

        return $response;
    }
}