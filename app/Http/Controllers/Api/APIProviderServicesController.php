<?php
namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Transformers\ProviderServicesTransformer;
use App\Http\Controllers\Api\BaseApiController;
use App\Repositories\ProviderServices\EloquentProviderServicesRepository;
use App\Models\Providers\Providers;
use App\Models\Services\Services;
use App\Models\ProviderServices\ProviderServices;

class APIProviderServicesController extends BaseApiController
{
    /**
     * ProviderServices Transformer
     *
     * @var Object
     */
    protected $providerservicesTransformer;

    /**
     * Repository
     *
     * @var Object
     */
    protected $repository;

    /**
     * PrimaryKey
     *
     * @var string
     */
    protected $primaryKey = 'providerservicesId';

    /**
     * __construct
     *
     */
    public function __construct()
    {
        $this->repository                       = new EloquentProviderServicesRepository();
        $this->providerservicesTransformer = new ProviderServicesTransformer();
    }

    /**
     * List of All ProviderServices
     *
     * @param Request $request
     * @return json
     */
    public function index(Request $request)
    {
        $userInfo   = $this->getAuthenticatedUser();
        $providerId = $request->has('provider_id') ?  $request->get('provider_id') : access()->getProviderId($userInfo->id);
        $provider   = Providers::with(['services'])->where('id', $providerId)->first();
        $services   = Services::getAll()->toArray();
       
        if(isset($provider) && count($provider))
        {
            $itemsOutput = $this->providerservicesTransformer->transformProviderWithServices($provider, $services);
            if(isset($itemsOutput) && count($itemsOutput))
            {
                return $this->successResponse($itemsOutput);
            }
        }

        return $this->setStatusCode(200)->failureResponse([
            'message' => 'No Services Found !'
            ], 'No Services Found !');
    }

    /**
     * List of All ProviderServices
     *
     * @param Request $request
     * @return json
     */
    public function search(Request $request)
    {
        $userInfo   = $this->getAuthenticatedUser();
        $providerId = $request->has('provider_id') ?  $request->get('provider_id') : $userInfo->id;
        $serviceIds = ProviderServices::where('provider_id', $providerId)-> pluck('service_id')->toArray();
        $services   = Services::getAll();
       
        if(isset($services) && count($services))
        {
            $itemsOutput = $this->providerservicesTransformer->transformProviderSearchServices($serviceIds, $services);
            
            return $this->successResponse($itemsOutput);
        }

        return $this->setStatusCode(400)->failureResponse([
            'message' => 'Unable to find ProviderServices!'
            ], 'No ProviderServices Found !');
    }

    /**
     * Create
     *
     * @param Request $request
     * @return string
     */
    public function create(Request $request)
    {
        if($request->has('service_id'))
        {
            $userInfo   = $this->getAuthenticatedUser();
            $providerId = $request->has('provider_id') ?  $request->get('provider_id') : $userInfo->id;
            $isExist    = ProviderServices::where([
                'provider_id'   => $providerId,
                'service_id'    => $request->get('service_id')
            ])->count();

            if(isset($isExist) && $isExist > 0)
            {
                return $this->setStatusCode(400)->failureResponse([
                    'reason' => 'Service Already Exists !'
                    ], 'Service Already Exists !');
            }

            $status = ProviderServices::create([
                'provider_id'   => $providerId,
                'service_id'    => $request->get('service_id')
            ]);
            
            if($status)
            {
                $message = [
                    'message' => 'Service added Successfully'
                ];
                return $this->successResponse($message, 'ProviderServices is Created Successfully');
            }
        }

        return $this->setStatusCode(400)->failureResponse([
            'reason' => 'Invalid Inputs'
            ], 'Something went wrong !');
    }

    /**
     * View
     *
     * @param Request $request
     * @return string
     */
    public function show(Request $request)
    {
        $itemId = (int) hasher()->decode($request->get($this->primaryKey));

        if($itemId)
        {
            $itemData = $this->repository->getById($itemId);

            if($itemData)
            {
                $responseData = $this->providerservicesTransformer->transform($itemData);

                return $this->successResponse($responseData, 'View Item');
            }
        }

        return $this->setStatusCode(400)->failureResponse([
            'reason' => 'Invalid Inputs or Item not exists !'
            ], 'Something went wrong !');
    }

    /**
     * Edit
     *
     * @param Request $request
     * @return string
     */
    public function edit(Request $request)
    {
        $itemId = (int) hasher()->decode($request->get($this->primaryKey));

        if($itemId)
        {
            $status = $this->repository->update($itemId, $request->all());

            if($status)
            {
                $itemData       = $this->repository->getById($itemId);
                $responseData   = $this->providerservicesTransformer->transform($itemData);

                return $this->successResponse($responseData, 'ProviderServices is Edited Successfully');
            }
        }

        return $this->setStatusCode(400)->failureResponse([
            'reason' => 'Invalid Inputs'
        ], 'Something went wrong !');
    }

    /**
     * Delete
     *
     * @param Request $request
     * @return string
     */
    public function delete(Request $request)
    {
        if($request->has('service_id'))
        {
            $userInfo   = $this->getAuthenticatedUser();
            $providerId = $request->has('provider_id') ?  $request->get('provider_id') : $userInfo->id;
            $isExist    = ProviderServices::where([
                'provider_id'   => $providerId,
                'service_id'    => $request->get('service_id')
            ])->first();

            if(isset($isExist->id))
            {
                if($isExist->delete())
                {
                    $message = [
                        'message' => 'Service removed Successfully'
                    ];
                    return $this->successResponse($message, 'Provider Services is removed Successfully');   
                }
            }

            return $this->setStatusCode(400)->failureResponse([
                    'reason' => 'No Service Exists !'
                    ], 'No Service Exists !');
        }

        return $this->setStatusCode(400)->failureResponse([
            'reason' => 'Invalid Inputs'
            ], 'Something went wrong !');
    }
}