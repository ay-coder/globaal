<?php
namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Transformers\ProvidersTransformer;
use App\Http\Controllers\Api\BaseApiController;
use App\Repositories\Providers\EloquentProvidersRepository;
use App\Models\Services\Services;
use App\Models\ProviderServices\ProviderServices;
use App\Models\CompanyProviders\CompanyProviders;

class APIProvidersController extends BaseApiController
{
    /**
     * Providers Transformer
     *
     * @var Object
     */
    protected $providersTransformer;

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
    protected $primaryKey = 'providersId';

    /**
     * __construct
     *
     */
    public function __construct()
    {
        $this->repository                       = new EloquentProvidersRepository();
        $this->providersTransformer = new ProvidersTransformer();
    }

    /**
     * List of All Providers
     *
     * @param Request $request
     * @return json
     */
    public function index(Request $request)
    {
        $perPage    = $request->get('per_page') ? $request->get('per_page') : 100;
        $offset     = $request->get('page') ? $request->get('page') : 0;
        $orderBy    = $request->get('orderBy') ? $request->get('orderBy') : 'id';
        $order      = $request->get('order') ? $request->get('order') : 'ASC';
        $items     = $this->repository->model->with(['companies', 'companies.company', 'services', 'services.service', 'user', 'leavelOfExperience', 'company'])
        ->orderBy($orderBy, $order)
        ->limit($perPage)
        ->offset($offset)
        ->get();

        if(isset($items) && count($items))
        {
            $itemsOutput = $this->providersTransformer->transformProviders($items);

            return $this->successResponse($itemsOutput);
        }

        return $this->setStatusCode(400)->failureResponse([
            'message' => 'Unable to find Providers!'
            ], 'No Providers Found !');
    }

    /**
     * Create
     *
     * @param Request $request
     * @return string
     */
    public function create(Request $request)
    {
        $model = $this->repository->create($request->all());

        if($model)
        {
            $responseData = $this->providersTransformer->transform($model);

            return $this->successResponse($responseData, 'Providers is Created Successfully');
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
        $userInfo   = $this->getAuthenticatedUser();
        $providerId = $request->has('provider_id') ? $request->get('provider_id') : $userInfo->id;

        $item       = $this->repository->model->with(['companies', 'companies.company', 'services', 'services.service', 'user', 'leavelOfExperience', 'company', 'credentials'])
        ->where('id', $providerId)
        ->first();

        if(isset($item) && count($item))
        {
            $itemsOutput = $this->providersTransformer->transformSingleProviders($item);

            return $this->successResponse($itemsOutput);
        }

        return $this->setStatusCode(400)->failureResponse([
            'reason' => 'Invalid Inputs or Item Provider exists !'
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
                $responseData   = $this->providersTransformer->transform($itemData);

                return $this->successResponse($responseData, 'Providers is Edited Successfully');
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
        $itemId = (int) hasher()->decode($request->get($this->primaryKey));

        if($itemId)
        {
            $status = $this->repository->destroy($itemId);

            if($status)
            {
                return $this->successResponse([
                    'success' => 'Providers Deleted'
                ], 'Providers is Deleted Successfully');
            }
        }

        return $this->setStatusCode(404)->failureResponse([
            'reason' => 'Invalid Inputs'
        ], 'Something went wrong !');
    }

    /**
     * Add Service
     *
     * @param Request $request
     * @return string
     */
    public function addService(Request $request)
    {
        if($request->has('service_id') && $request->get('service_id'))
        {
            $service    = Services::find($request->get('service_id'));
            $userInfo   = $this->getAuthenticatedUser();
            $providerId = $userInfo->id;

            if($service)
            {
                $isExist = ProviderServices::where([
                    'provider_id'   => $userInfo->id,
                    'service_id'    => $request->get('service_id')
                ])->count();

                if(isset($isExist) && $isExist > 0 )
                {
                    return $this->setStatusCode(400)->failureResponse([
                        'reason' => 'Already Service Added!'
                        ], 'Already Service Added !');   
                }

                $status = ProviderServices::create([
                    'provider_id'   => $userInfo->id,
                    'service_id'    => $request->get('service_id')
                ]);

                if($status)
                {
                    $item       = $this->repository->model->with(['services', 'services.service', 'user', 'leavelOfExperience', 'company'])
                    ->where('id', $providerId)
                    ->first();

                    if(isset($item) && count($item))
                    {
                        $itemsOutput = $this->providersTransformer->transformSingleProviders($item);

                        return $this->successResponse($itemsOutput);
                    } 
                }
            }
        }
       
        return $this->setStatusCode(400)->failureResponse([
            'reason' => 'Invalid Inputs or Item Provider exists !'
            ], 'Something went wrong !');       
    }

    /**
     * Remove Service
     *
     * @param Request $request
     * @return string
     */
    public function removeService(Request $request)
    {
        if($request->has('service_id') && $request->get('service_id'))
        {
            $userInfo   = $this->getAuthenticatedUser();
            $providerId = $userInfo->id;
            $status     = ProviderServices::where([
                'provider_id'   => $userInfo->id,
                'service_id'    => $request->get('service_id')
            ])->delete();

            if($status)
            {
                $item       = $this->repository->model->with(['services', 'services.service', 'user', 'leavelOfExperience', 'company'])
                ->where('id', $providerId)
                ->first();

                if(isset($item) && count($item))
                {
                    $itemsOutput = $this->providersTransformer->transformSingleProviders($item);

                    return $this->successResponse($itemsOutput);
                } 
            }
        }
       
        return $this->setStatusCode(400)->failureResponse([
            'reason' => 'Invalid Inputs or No Service exists !'
            ], 'Something went wrong !');       
    }

    /**
     * Company Requests
     * 
     * @param  Request $request [description]
     * @return json
     */
    public function companyRequests(Request $request)
    {
        $userInfo   = $this->getAuthenticatedUser();
        $requests   = CompanyProviders::where([
            'provider_id'           => $userInfo->id,
            'accept_by_provider'    => 0
        ])->with(['company', 'provider'])
        ->orderBy('id', 'desc')
        ->get();
        

        if($requests && count($requests))
        {
            $itemsOutput = $this->providersTransformer->transCompanyRequests($requests);

            return $this->successResponse($itemsOutput);
        }
       
        return $this->setStatusCode(400)->failureResponse([
            'reason' => 'Invalid Inputs or No Service exists !'
            ], 'Something went wrong !');       
    }

    /**
     * Accept Company Requests
     * 
     * @param  Request $request [description]
     * @return json
     */
    public function acceptCompanyRequests(Request $request)
    {
        if($request->has('request_id'))
        {
            $userInfo   = $this->getAuthenticatedUser();
            $request    = CompanyProviders::where([
                'provider_id'           => $userInfo->id,
                'id'                    => $request->get('request_id'),
                'accept_by_provider'    => 0
            ])
            ->first();
        
            if($request && count($request))
            {   
                $request->accept_by_provider = 1;

                if($request->save())
                {
                    return $this->successResponse(['message' => 'Accepted Request Successfully!'], 'Accepted Request Successfully');
                }
            }
        }

        return $this->setStatusCode(400)->failureResponse([
            'reason' => 'Invalid Inputs or No Request exists !'
            ], 'Something went wrong !');       
    }

    /**
     * Reject Company Requests
     * 
     * @param  Request $request [description]
     * @return json
     */
    public function rejectCompanyRequests(Request $request)
    {
        if($request->has('request_id'))
        {
            $userInfo   = $this->getAuthenticatedUser();
            $request    = CompanyProviders::where([
                'provider_id'           => $userInfo->id,
                'id'                    => $request->get('request_id'),
                'accept_by_provider'    => 0
            ])
            ->first();
        
            if($request && count($request))
            {   
                if($request->delete())
                {
                    return $this->successResponse(['message' => 'Rejected Request Successfully!'], 'Rejected Request Successfully');
                }
            }
        }
        
        return $this->setStatusCode(400)->failureResponse([
            'reason' => 'Invalid Inputs or No Request exists !'
            ], 'Something went wrong !');       
    }
}