<?php
namespace App\Http\Transformers;

use App\Http\Transformers;

class PatientTransformer extends Transformer
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
            "patientId" => (int) $item->id, "patientUserId" =>  $item->user_id, "patientCreatedAt" =>  $item->created_at, "patientUpdatedAt" =>  $item->updated_at, 
        ];
    }
}