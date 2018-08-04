<?php
namespace App\Http\Transformers;

use App\Http\Transformers;
use URL;

class MessagesTransformer extends Transformer
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
            "messagesId" => (int) $item->id, "messagesUserId" =>  $item->user_id, "messagesProviderId" =>  $item->provider_id, "messagesPatientId" =>  $item->patient_id, "messagesMessage" =>  $item->message, "messagesIsRead" =>  $item->is_read, "messagesCreatedAt" =>  $item->created_at, "messagesUpdatedAt" =>  $item->updated_at, 
        ];
    }

    public function messageTranform($items)
    {
        $response = [];

        if($items)   
        {
            $currentUserId = access()->user()->id;
            foreach($items as $item)
            {
                $isRead     = $currentUserId == $item->user_id ? 1 : $item->is_read;
                $response[] = [
                    'message_id'    => $item->id,
                    'user_id'       => $item->user_id,
                    'provider_id'   => $item->provider_id,
                    'patient_id'    => $item->patient_id,
                    'message'       => $item->message,
                    'provider_name' => $item->provider->name,
                    'provider_pic'  => URL::to('/').'/uploads/user/' . $item->provider->profile_pic,
                    'patient_name'  => $item->patient->name,
                    'patient_pic'   => URL::to('/').'/uploads/user/' . $item->patient->profile_pic,
                    'is_read'       => $isRead
                ];
            }
        }

        return $response;
    }
}