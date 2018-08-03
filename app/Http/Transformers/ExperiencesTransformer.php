<?php
namespace App\Http\Transformers;

use App\Http\Transformers;

class ExperiencesTransformer extends Transformer
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
            "experience_id"         => (int) $item->id,
            "level_of_experience"   =>  $item->level_of_experience
        ];
    }

    public function experinceTransform($items)
    {
        $response = [];

        foreach($items as $item)
        {
            $response[] =  [
                "experience_id"         => (int) $item->id,
                "level_of_experience"   =>  $item->level_of_experience
            ];
        }

        return $response;
    }
}