<?php
namespace App\Http\Transformers;

use App\Http\Transformers;

class MasterCategoriesTransformer extends Transformer
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
            "mastercategoriesId" => (int) $item->id, "mastercategoriesTitle" =>  $item->title, "mastercategoriesDescription" =>  $item->description, "mastercategoriesCreatedAt" =>  $item->created_at, "mastercategoriesUpdatedAt" =>  $item->updated_at, 
        ];
    }

    public function masterCategoryTransform($items)
    {
        $response = [];

        foreach($items as $item)
        {
            $services = [];

            if(isset($item->services))
            {
                foreach($item->services as $service)
                {
                    $services[] = [
                        'id'    => $service->id,
                        'title' => $service->title
                    ];
                }
            }
            $response[] = [
                'id'            => $item->id,
                'title'         => $item->title,
                'description'   => $item->description,
                'services'      => $services
            ];
        }

        return $response;
    }
}