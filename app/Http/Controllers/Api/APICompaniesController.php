<?php
namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Transformers\CompaniesTransformer;
use App\Http\Controllers\Api\BaseApiController;
use App\Repositories\Companies\EloquentCompaniesRepository;
use App\Repositories\Providers\EloquentProvidersRepository;
use App\Models\CompanyProviders\CompanyProviders;
use App\Http\Transformers\ProvidersTransformer;
use App\Models\Access\User\User;
use App\Models\Providers\Providers;
use DB;

class APICompaniesController extends BaseApiController
{
    /**
     * Companies Transformer
     *
     * @var Object
     */
    protected $companiesTransformer;

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
    protected $primaryKey = 'companiesId';

    /**
     * __construct
     *
     */
    public function __construct()
    {
        $this->repository                       = new EloquentCompaniesRepository();
        $this->providerRepository   = new EloquentProvidersRepository();
        $this->companiesTransformer = new CompaniesTransformer();
        $this->providersTransformer = new ProvidersTransformer();
    }

    /**
     * List of All Companies
     *
     * @param Request $request
     * @return json
     */
    public function index(Request $request)
    {
        $paginate   = $request->get('paginate') ? $request->get('paginate') : false;
        $orderBy    = $request->get('orderBy') ? $request->get('orderBy') : 'id';
        $order      = $request->get('order') ? $request->get('order') : 'ASC';
        $items      = $paginate ? $this->repository->model->orderBy($orderBy, $order)->paginate($paginate)->items() : $this->repository->getAll($orderBy, $order);

        if(isset($items) && count($items))
        {
            $itemsOutput = $this->companiesTransformer->companyTranform($items);

            return $this->successResponse($itemsOutput);
        }

        return $this->setStatusCode(400)->failureResponse([
            'message' => 'Unable to find Companies!'
            ], 'No Companies Found !');
    }

    public function getCompaniesWithDistances(Request $request)
    {
        $userInfo   = $this->getAuthenticatedUser();
        $lat        = $request->has('lat') ? $request->get('lat') : $userInfo->lat;
        $long       = $request->has('long') ? $request->get('long') : $userInfo->long;
        $companies   = DB::select("SELECT *, ( 6371 * acos( cos( radians($lat) ) * cos( radians( `lat` ) ) * cos( radians( `long` ) - radians($long
            ) ) + sin( radians($lat) ) * sin( radians( `lat` ) ) ) ) AS distance
        FROM users
        where user_type = 3
        AND users.id != $userInfo->id
        ORDER BY distance ASC");
        
        $allCompanies = $this->repository->model->getAll();
        
        if(isset($companies) && count($companies))
        {
            $itemsOutput = $this->companiesTransformer->companyTranformWithDistance($companies, $allCompanies);

            return $this->successResponse($itemsOutput);
        }

        return $this->setStatusCode(400)->failureResponse([
         'message' => 'Unable to find Companies!'
        ], 'No Companies Found !');
    }

    /**
     * Get All
     *
     * @param Request $request
     * @return json
     */
    public function getAll(Request $request)
    {
        $perPage    = $request->get('per_page') ? $request->get('per_page') : 100;
        $offset     = $request->get('page') ? $request->get('page') : 0;
        $orderBy    = $request->get('orderBy') ? $request->get('orderBy') : 'id';
        $order      = $request->get('order') ? $request->get('order') : 'ASC';
        $items      = $paginate ? $this->repository->model->orderBy($orderBy, $order)->limit($perPage, $offset)->items() : $this->repository->getAll($orderBy, $order);

        if(isset($items) && count($items))
        {
            $itemsOutput = $this->companiesTransformer->companyTranform($items);

            return $this->successResponse($itemsOutput);
        }

        return $this->setStatusCode(400)->failureResponse([
            'message' => 'Unable to find Companies!'
            ], 'No Companies Found !');
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
            $responseData = $this->companiesTransformer->transform($model);

            return $this->successResponse($responseData, 'Companies is Created Successfully');
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
        if($request->has('company_id'))
        {
            $companyId      = $request->get('company_id');
            //$companyUser    = User::where('id', $companyId)->first();
            /*$company        = $this->repository->model->with(['company_providers', 'company_services', 'company_testimonials'])->where()->first();*/


            $companyInfo      = $this->repository->model->with(['company_all_providers', 'user', 'company_all_providers.provider.user', 'company_providers', 'company_providers.provider', 'company_services', 'company_testimonials', 'company_services', 'company_services.service'])
            ->where('id', $companyId)
            ->first();

            if(isset($companyInfo))
            {
                $responseData = $this->companiesTransformer->singleCompanyTransform($companyInfo);

                return $this->successResponse($responseData);
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
                $responseData   = $this->companiesTransformer->transform($itemData);

                return $this->successResponse($responseData, 'Companies is Edited Successfully');
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
                    'success' => 'Companies Deleted'
                ], 'Companies is Deleted Successfully');
            }
        }

        return $this->setStatusCode(404)->failureResponse([
            'reason' => 'Invalid Inputs'
        ], 'Something went wrong !');
    }

    /**
     * Get All Providers
     * 
     * @param Request $request
     * @return json
     */
    public function getAllProviders(Request $request)
    {
        $perPage    = $request->get('per_page') ? $request->get('per_page') : 100;
        $offset     = $request->get('page') ? $request->get('page') : 0;
        $orderBy    = $request->get('orderBy') ? $request->get('orderBy') : 'id';
        $order      = $request->get('order') ? $request->get('order') : 'ASC';
        $items      = $this->repository->model->with(['company_providers', 'company_providers.provider', 'company_providers.provider.user',])
        ->orderBy($orderBy, $order)
        ->limit($perPage)
        ->offset($offset)
        ->get();

        if(isset($items) && count($items))
        {
            $itemsOutput = $this->companiesTransformer->companyTranformWithProviders($items);

            return $this->successResponse($itemsOutput);
        }

        return $this->setStatusCode(400)->failureResponse([
            'message' => 'Unable to find Companies!'
            ], 'No Companies Found !');       
    }

    /**
     * Get Company Providers
     * 
     * @param Request $request
     * @return json
     */
    public function getCompanyProviders(Request $request)
    {
        if($request->has('company_id'))
        {
            $item      = $this->repository->model->with(['company_providers', 'company_providers.provider'])
            ->where('id', $request->get('company_id'))
            ->get();

            if(isset($item) && count($item))
            {
                $itemsOutput = $this->companiesTransformer->companyTranformWithProviders($item);

                return $this->successResponse($itemsOutput);
            }
        }

        return $this->setStatusCode(400)->failureResponse([
            'message' => 'Unable to find Companies!'
            ], 'No Companies Found !');       
    }

    /**
     * Add Provider
     * 
     * @param Request $request
     * @return json
     */
    public function addProvider(Request $request)
    {
        if($request->has('provider_id'))
        {
            $userInfo   = $this->getAuthenticatedUser();
            $companyId  = access()->getCompanyId($userInfo->id);

            if(isset($companyId) && is_numeric($companyId))
            {
                $companyInfo = $this->repository->model->with('company_all_providers')
                ->where([
                    'id' => $companyId
                ])->first();

                $isExist = $companyInfo->company_all_providers->where('provider_id', $request->get('provider_id'));

                if(isset($isExist) && count($isExist))
                {
                    return $this->setStatusCode(400)->failureResponse([
                        'message' => 'Provider either exists or Requested to Join'
                        ], 'Provider either exists or Requested to Join');
                }

                $status = CompanyProviders::create([
                    'provider_id'       => $request->get('provider_id'),
                    'company_id'        => $companyId,
                    'accept_by_company' => 1
                ]);

                if($status)
                {

                    $text       = $userInfo->company->company_name . " has requested to add you to it's provider list";

                    $provider   = Providers::with('user')->where('id', $request->get('provider_id'))->first();
                    $payload    = [
                            'mtitle'        => '',
                            'mdesc'         => $text,
                            'provider_id'   => $request->get('provider_id'),
                            'company_id'    => $companyId,
                            'ntype'         => 'COMPANY_CREATE_REQUEST'
                    ];

                    
                    $storeNotification = [
                        'user_id'       => $provider->user->id,
                        'title'         => $text,
                        'company_id'    => $companyId,
                        'provider_id'   => $request->get('provider_id'),
                        'notification_type' => 'COMPANY_CREATE_REQUEST'
                    ];

                    // Add Notification
                    access()->addNotification($storeNotification);

                    // Push Notification
                    access()->sentPushNotification($provider->user, $payload);

                    $message = [
                        'message' => 'Requeset sent to Provider successfully.'
                    ];
                    return $this->successResponse($message, 'Requeset sent to Provider successfully.');
                }
            }
        }

        return $this->setStatusCode(400)->failureResponse([
            'message' => 'Invalid Company or Provider!'
            ], 'Invalid Company or Provider');       
    }

    /**
     * Remove Provider
     * 
     * @param Request $request
     * @return json
     */
    public function removeProvider(Request $request)
    {
        if($request->has('provider_id'))
        {
            $userInfo   = $this->getAuthenticatedUser();
            $companyId  = access()->getCompanyId($userInfo->id);

            if(isset($companyId) && is_numeric($companyId))
            {
                $companyInfo = $this->repository->model->with('company_all_providers')
                ->where([
                    'id' => $companyId
                ])->first();

                $isExist = $companyInfo->company_all_providers->where('provider_id', $request->get('provider_id'));

                if(!isset($isExist))
                {
                    return $this->setStatusCode(400)->failureResponse([
                        'message' => 'No Provider exists'
                        ], 'No Provider exists');
                }

                $status = CompanyProviders::where([
                    'provider_id'       => $request->get('provider_id'),
                    'company_id'        => $companyId
                ])->delete();

                if($status)
                {
                    $message = [
                        'message' => 'Provider removed successfully.'
                    ];
                    return $this->successResponse($message, 'Provider removed successfully.');
                }
            }
        }

        return $this->setStatusCode(400)->failureResponse([
            'message' => 'Invalid Company or Provider!'
            ], 'Invalid Company or Provider');       
    }
    

    /**
     * Provider Requests
     * 
     * @param  Request $request [description]
     * @return json
     */
    public function providerRequests(Request $request)
    {
        $userInfo   = $this->getAuthenticatedUser();
        $requests   = CompanyProviders::where([
            'company_id'           => $userInfo->company->id,
            'accept_by_company'    => 0
        ])->with(['company', 'company.user', 'provider', 'provider.user'])
        ->orderBy('id', 'desc')
        ->get();
        

        if($requests && count($requests))
        {
            $itemsOutput = $this->providersTransformer->transCompanyRequests($requests);

            return $this->successResponse($itemsOutput);
        }
       
        return $this->setStatusCode(400)->failureResponse([
            'reason' => 'No Requests found!'
            ], 'No Requests found!');       
    }

    /**
     * Accept Provider Requests
     * 
     * @param  Request $request [description]
     * @return json
     */
    public function acceptProviderRequests(Request $request)
    {
        if($request->has('request_id'))
        {
            $userInfo   = $this->getAuthenticatedUser();
            $request    = CompanyProviders::where([
                'company_id'            => $userInfo->company->id,
                'id'                    => $request->get('request_id'),
                'accept_by_company'     => 0
            ])
            ->first();
        
            if($request && count($request))
            {   
                $request->accept_by_company = 1;



                if($request->save())
                {
                    $text       = $userInfo->name . ' has accepted your request';
                    $provider   = Providers::with('user')->where('id', $request->provider_id)->first();
                    $payload    = [
                            'mtitle'        => '',
                            'mdesc'         => $text,
                            'provider_id'   => $request->provider_id,
                            'company_id'    => $userInfo->company->id,
                            'ntype'         => 'COMPANY_ACCEPT_REQUEST'
                    ];

                    
                    $storeNotification = [
                        'user_id'       => $provider->user->id,
                        'title'         => $text,
                        'company_id'    => $userInfo->company->id,
                        'provider_id'   => $request->provider_id,
                        'notification_type' => 'COMPANY_ACCEPT_REQUEST'
                    ];

                    // Add Notification
                    access()->addNotification($storeNotification);

                    // Push Notification
                    access()->sentPushNotification($provider->user, $payload);

                    return $this->successResponse(['message' => 'Accepted Request Successfully!'], 'Accepted Request Successfully');
                }
            }
        }

        return $this->setStatusCode(400)->failureResponse([
            'reason' => 'Invalid Inputs or No Request exists !'
            ], 'Something went wrong !');       
    }

    /**
     * Reject Provider Requests
     * 
     * @param  Request $request [description]
     * @return json
     */
    public function rejectProviderRequests(Request $request)
    {
        if($request->has('request_id'))
        {
            $userInfo   = $this->getAuthenticatedUser();
            $request    = CompanyProviders::where([
                'company_id'            => $userInfo->company->id,
                'id'                    => $request->get('request_id'),
                'accept_by_company'     => 0
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

    /**
     * Search Providers
     * 
     * @return array
     */
    public function searchProviders(Request $request)
    {
        $userInfo   = $this->getAuthenticatedUser();
        $companyId  = $request->has('company_id') ? $request->get('company_id') : access()->getCompanyId($userInfo->id);
        if($companyId)
        {
            $companyProviders   = CompanyProviders::where('company_id', $companyId)->pluck('provider_id')->toArray();
            $items              = Providers::with('user')->whereNotIn('id', $companyProviders)->get();


            if(isset($items) && count($items))
            {
                $itemsOutput = $this->companiesTransformer->companyTranformSearchProviders($items);

                return $this->successResponse($itemsOutput);
            }
        }

        return $this->setStatusCode(400)->failureResponse([
            'message' => 'Unable to find Companies!'
            ], 'No Companies Found !');    
    }

    /**
     * List of All Company Providers
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

        $query      = $this->providerRepository->model->whereHas('services', function($q) use($serviceId)
        {
            if(count($serviceId))
            {
                $q->whereIn('service_id', $serviceId);
            }
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


       /* if($lat && $long)
        {
            $distance   = DB::select("SELECT id, ( 6371 * acos( cos( radians($lat) ) * cos( radians( `lat` ) ) * cos( radians( `long` ) - radians($long
                ) ) + sin( radians($lat) ) * sin( radians( `lat` ) ) ) ) AS distance
            FROM users
            where user_type = 2
            ORDER BY distance ASC");
            $distance = collect($distance);
        }*/

        $providerIds = $query->pluck('id')->toArray();
        

        /*$items = $items->map(function($item) use($distance)
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
        });*/

        if($lat && $long)
        {
            $distance   = DB::select("SELECT id, ( 6371 * acos( cos( radians($lat) ) * cos( radians( `lat` ) ) * cos( radians( `long` ) - radians($long
                ) ) + sin( radians($lat) ) * sin( radians( `lat` ) ) ) ) AS distance
            FROM users
            where user_type = 1
            ORDER BY distance ASC");

            $distance = collect($distance);
        }
        $items  = $this->repository->model->whereHas('company_all_providers', function($q) use($providerIds)
            {
                $q->whereIn('provider_id', $providerIds);
            })->with([
            'user',
            'company_all_providers','company_all_providers.provider.user',
            'company_providers', 'company_providers.provider',
            'company_providers.provider.services.service',  
            'company_services', 'company_testimonials', 
            'company_services', 'company_services.service'
            ])->get();

        $items = $items->map(function($item) use($distance)
        {
            $item->distance = 0;

            if(isset($distance) && count($distance))
            {
                $distanceUser   = $distance->where('id', $item->user->id);
                if(isset($distanceUser) && isset($distanceUser->distance))
                {
                    $item->distance = $distanceUser->distance;
                }
            }

            return $item;
        }); 
        

        if(isset($items) && count($items))
        {
            $itemsOutput = $this->companiesTransformer->companyTranformFilterWithProviders($items);

            return $this->successResponse($itemsOutput);
        }


       

        return $this->setStatusCode(400)->failureResponse([
            'message' => 'No Companies Found!'
            ], 'No Companies Found!');
    }
}