<?php
namespace App\Http\Transformers;

use App\Http\Transformers;

class CredentialsTransformer extends Transformer
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
            "credentialsId" => (int) $item->id, "credentialsProviderId" =>  $item->provider_id, "credentialsTitle" =>  $item->title, "credentialsDescription" =>  $item->description, "credentialsStatus" =>  $item->status, "credentialsCreatedAt" =>  $item->created_at, "credentialsUpdatedAt" =>  $item->updated_at, 
        ];
    }
    
    /**
     * Show All Credential Tranform
     * 
     * @param array $items
     * @return array
     */
    public function showAllCredentialTranform($items)
    {
        $response = [];

        if($items)
        {
            foreach($items as $item)
            {
                if(isset($item->provider))
                {
                    $response[] = [
                        'credential_id' => (int) $item->id,
                        'title'         => $item->title,
                        'provider_id'   => (int) $item->provider_id,
                        'provider_name' => $item->provider->user->name
                    ];
                }
            }
        }

        return $response;
    }
    
}