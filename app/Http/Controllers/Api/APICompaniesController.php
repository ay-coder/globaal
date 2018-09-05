<?php
namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Transformers\CompaniesTransformer;
use App\Http\Controllers\Api\BaseApiController;
use App\Repositories\Companies\EloquentCompaniesRepository;
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
        $companies   = DB::select("SELECT *, ( 6371 * acos( cos( radians($userInfo->lat) ) * cos( radians( `lat` ) ) * cos( radians( `long` ) - radians($userInfo->long) ) + sin( radians($userInfo->lat) ) * sin( radians( `lat` ) ) ) ) AS distance
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
            $companyUser    = User::where('id', $companyId)->first();
            /*$company        = $this->repository->model->with(['company_providers', 'company_services', 'company_testimonials'])->where()->first();*/


            $companyInfo      = $this->repository->model->with(['company_providers', 'company_providers.provider', 'company_services', 'company_testimonials', 'company_services', 'company_services.service'])
            ->where('user_id', $companyUser->id)
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
        $items      = $this->repository->model->with(['company_providers', 'company_providers.provider'])
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
        $perPage    = $request->get('per_page') ? $request->get('per_page') : 100;
        $offset     = $request->get('page') ? $request->get('page') : 0;
        $orderBy    = $request->get('orderBy') ? $request->get('orderBy') : 'id';
        $order      = $request->get('order') ? $request->get('order') : 'ASC';
        $items      = $this->repository->model->with(['providers', 'providers.user'])
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
        ])->with(['company', 'company.user', 'provider'])
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
}