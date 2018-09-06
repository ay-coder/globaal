<?php
namespace App\Http\Transformers;

use App\Http\Transformers;
use URL;

class AppointmentsTransformer extends Transformer
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
            "appointmentsId" => (int) $item->id, "appointmentsUserId" =>  $item->user_id, "appointmentsProviderId" =>  $item->provider_id, "appointmentsServiceId" =>  $item->service_id, "appointmentsCompanyId" =>  $item->company_id, "appointmentsBookingDate" =>  $item->booking_date, "appointmentsStartTime" =>  $item->start_time, "appointmentsEndTime" =>  $item->end_time, "appointmentsCurrentStatus" =>  $item->current_status, "appointmentsStatus" =>  $item->status, "appointmentsCreatedAt" =>  $item->created_at, "appointmentsUpdatedAt" =>  $item->updated_at, 
        ];
    }

    public function showAllAppointments($items)
    {
        $response = [];

        if(isset($items))   
        {
            foreach($items as $item)
            {
                $response[] = [
                    'appointment_id'    => (int) $item->id,
                    'user_id'           => (int) $item->user_id,
                    'company_id'        => (int) $item->company_id,
                    'service_id'        => (int) $item->service_id,
                    'service_title'     => isset($item->service) ? $item->service->title : '',
                    'provider_id'       => (int) $item->provider_id,
                    'provider_name'     => $item->provider->name,
                    'provider_pic'      => URL::to('/').'/uploads/user/' . $item->provider->profile_pic, 
                    'booking_date'      => date('Y-m-d', strtotime($item->booking_date)),
                    'start_time'        => $item->start_time,
                    'end_time'          => $item->end_time,
                    'current_status'    => $item->current_status
                ];
            }
        }
        return $response;
    }
}