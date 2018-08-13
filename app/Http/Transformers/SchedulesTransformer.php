<?php
namespace App\Http\Transformers;

use App\Http\Transformers;

class SchedulesTransformer extends Transformer
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
            "schedulesId" => (int) $item->id, "schedulesUserId" =>  $item->user_id, "schedulesProviderId" =>  $item->provider_id, "schedulesServiceId" =>  $item->service_id, "schedulesCompanyId" =>  $item->company_id, "schedulesDayName" =>  $item->day_name, "schedulesStartTime" =>  $item->start_time, "schedulesEndTime" =>  $item->end_time, "schedulesStatus" =>  $item->status, "schedulesCreatedAt" =>  $item->created_at, "schedulesUpdatedAt" =>  $item->updated_at, 
        ];
    }

    public function transformProviderSchedules($items)
    {
        $response = [];

        if($items)
        {
            foreach($items as $item)
            {
                $response[$item->day_name][] = [
                    'schedule_id'   => (int) $item->id,
                    'provider_id'   => (int) $item->provider_id,
                    'service_id'    => (int) $item->service_id,
                    'company_id'    => (int) $item->company_id,
                    'provider_name' => $item->provider->user->name,
                    'company_name'  => $item->company->company_name,
                    'service'       => $item->service->title,
                    'start_time'    => $item->start_time,
                    'end_time'      => $item->end_time,
                ];
            }

        }

        return $response;
    }
}