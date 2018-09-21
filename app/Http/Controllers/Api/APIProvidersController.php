<?php 

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Transformers\ProvidersTransformer;
use App\Http\Controllers\Api\BaseApiController;
use App\Repositories\Providers\EloquentProvidersRepository;
use App\Models\Services\Services;
use App\Models\ProviderServices\ProviderServices;
use App\Models\CompanyProviders\CompanyProviders;
use App\Models\Companies\Companies;
use DB;

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
        $providerId = $request->has('provider_id') ? $request->get('provider_id') : access()->getProviderId($userInfo->id);

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
        $providerId = access()->getProviderId($userInfo->id);

        if(isset($providerId))
        {
            $requests   = CompanyProviders::where([
                'provider_id'           => $providerId,
                'accept_by_provider'    => 0,
                'accept_by_company'     => 1
            ])->with(['company', 'company.user', 'provider'])
            ->orderBy('id', 'desc')
            ->get();

            if($requests && count($requests))
            {
                $itemsOutput = $this->providersTransformer->transCompanyRequests($requests);

                return $this->successResponse($itemsOutput);
            }
        }
        
       
        return $this->setStatusCode(400)->failureResponse([
            'reason' => 'Invalid Inputs or No Requests exists !'
            ], 'No Data Found');       
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
            $providerId = access()->getProviderId($userInfo->id);

            if(isset($providerId))
            {
                $request    = CompanyProviders::where([
                    'provider_id'           => $providerId,
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
            $providerId = access()->getProviderId($userInfo->id);
            
            if(isset($providerId))
            {
                $request    = CompanyProviders::where([
                    'provider_id'           => $providerId,
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
        
        }
        
        return $this->setStatusCode(400)->failureResponse([
            'reason' => 'Invalid Inputs or No Request exists !'
            ], 'Something went wrong !');       
    }

    /**
     * Add Company
     * 
     * @param Request $request
     */
    public function addCompany(Request $request)
    {
        if($request->has('company_id'))
        {
            $userInfo   = $this->getAuthenticatedUser();
            $providerId = access()->getProviderId($userInfo->id);
            
            if(isset($providerId) && is_numeric($providerId))
            {
               $providerInfo = $this->repository->model->with('all_companies')
               ->where('id', $providerId)->first();

               if(isset($providerInfo))
               {    
                    $isExist = $providerInfo->all_companies->where('company_id', $request->get('company_id'));

                    if(isset($isExist) && count($isExist))
                    {
                        return $this->setStatusCode(400)->failureResponse([
                        'message' => 'Company either exists or Requested to Join'
                        ], 'Company either exists or Requested to Join');
                    }

                    $status = CompanyProviders::create([
                        'provider_id'   => $providerId,
                        'company_id'    => $request->get('company_id'),
                        'accept_by_provider' => 1
                    ]);

                    if($status)
                    {
                        $message = [
                            'message' => 'Requeset sent to Company successfully.'
                        ];
                        return $this->successResponse($message, 'Requeset sent to Company successfully.');
                    }
                }
            }
        }

        return $this->setStatusCode(400)->failureResponse([
            'reason' => 'Invalid Inputs or No Company Exists!'
            ], 'Invalid Inputs or No Company Exists');     
    }


    /**
     * Remove Company
     * 
     * @param Request $request
     */
    public function removeCompany(Request $request)
    {
        if($request->has('company_id'))
        {
            $userInfo   = $this->getAuthenticatedUser();
            $providerId = access()->getProviderId($userInfo->id);
            
            if(isset($providerId) && is_numeric($providerId))
            {
               $providerInfo = $this->repository->model->with('all_companies')
               ->where('id', $providerId)->first();

               if(isset($providerInfo))
               {    
                    $isExist = $providerInfo->all_companies->where('company_id', $request->get('company_id'));

                    if(!isset($isExist))
                    {
                        return $this->setStatusCode(400)->failureResponse([
                            'reason' => 'No Company Exists !'
                            ], 'No Company Exists !');  
                    }

                    $status = CompanyProviders::where([
                        'provider_id'   => $providerId,
                        'company_id'    => $request->get('company_id')
                    ])->delete();

                    if($status)
                    {
                        $message = [
                            'message' => 'Company removed successfully.'
                        ];
                        return $this->successResponse($message, 'Company removed successfully.');
                    }
                }
            }
        }

        return $this->setStatusCode(400)->failureResponse([
            'reason' => 'Invalid Inputs or No Company Exists!'
            ], 'Invalid Inputs or No Company Exists');     
    }

    /**
     * Search Company
     * 
     * @return array
     */
    public function searchCompany(Request $request)
    {
        $userInfo   = $this->getAuthenticatedUser();
        $providerId = $request->has('provider_id') ? $request->get('provider_id') : access()->getProviderId($userInfo->id);

        if($providerId)
        {
            $providerCompanies   = CompanyProviders::where('provider_id', $providerId)->pluck('company_id')->toArray();

            $items              = Companies::with('user')->whereNotIn('id', $providerCompanies)->get();

            if(isset($items) && count($items))
            {
                $itemsOutput = $this->providersTransformer->providerTransformCompanies($items);

                return $this->successResponse($itemsOutput);
            }
        }

        return $this->setStatusCode(400)->failureResponse([
            'message' => 'Unable to find Companies!'
            ], 'No Companies Found !');    
    }

    /**
     * List of All Providers
     *
     * @param Request $request
     * @return json
     */
    public function filter(Request $request)
    {
        $serviceId  = $request->has('services') ? explode(',', $request->get('services')) : [];
        $keyword    = $request->has('keyword') ? $request->get('keyword') : false;
        $experience = $request->has('experience') ? explode(',', $request->get('experience')) : false;
        $distance   = [];
        $lat        = $request->has('lat') ? $request->get('lat') : false;
        $long       = $request->has('long') ? $request->get('long') : false;

        $query      = $this->repository->model->whereHas('services', function($q) use($serviceId)
        {
            $q->whereIn('service_id', $serviceId);
        });

        if($experience)
        {
            $query->whereIn('level_of_experience',$experience);
        }

        if($keyword)
        {
            $query->whereHas('user', function($q) use($keyword)
            {   
                $q->where('name', 'LIKE', "%$keyword%");
            });
        }

        if($lat && $long)
        {
            $distance   = DB::select("SELECT id, ( 6371 * acos( cos( radians($lat) ) * cos( radians( `lat` ) ) * cos( radians( `long` ) - radians($long
                ) ) + sin( radians($lat) ) * sin( radians( `lat` ) ) ) ) AS distance
            FROM users
            where user_type = 2
            ORDER BY distance ASC");
            $distance = collect($distance);
        }

        $items = $query->with([
            'companies', 'companies.user',
            'companies.company', 'services', 
            'services.service', 'user', 'leavelOfExperience', 'company'
        ])
        ->get();

        $items = $items->map(function($item) use($distance)
        {
            if(isset($distance) && count($distance))
            {
                $singleUser = $distance->where('id', $item->user_id);
                
                if(isset($singleUser) && isset($singleUser->distance))
                {
                    $item->distance = $singleUser->distance;
                }
            }

            $item->distance = null;

            return $item;
        });


        $items = $items->sortBy('distance');
        if(isset($items) && count($items))
        {
            $itemsOutput = $this->providersTransformer->transformProviders($items);

            return $this->successResponse($itemsOutput);
        }

        return $this->setStatusCode(400)->failureResponse([
            'message' => 'Unable to find Providers!'
            ], 'No Providers Found !');
    }
}