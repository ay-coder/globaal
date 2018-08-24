<?php
namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Transformers\CompanyServicesTransformer;
use App\Http\Controllers\Api\BaseApiController;
use App\Repositories\CompanyServices\EloquentCompanyServicesRepository;
use App\Models\Companies\Companies;
use App\Models\Services\Services;

class APICompanyServicesController extends BaseApiController
{
    /**
     * CompanyServices Transformer
     *
     * @var Object
     */
    protected $companyservicesTransformer;

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
    protected $primaryKey = 'companyservicesId';

    /**
     * __construct
     *
     */
    public function __construct()
    {
        $this->repository                       = new EloquentCompanyServicesRepository();
        $this->companyservicesTransformer = new CompanyServicesTransformer();
    }

    /**
     * List of All CompanyServices
     *
     * @param Request $request
     * @return json
     */
    public function index(Request $request)
    {
        if($request->has('company_id'))
        {  
            $allServices    = Services::pluck('title', 'id')->toArray();
            $company        = Companies::where('user_id', $request->get('company_id'))
                ->with(['company_services'])->first();

            if(isset($company))
            {
                $itemsOutput = $this->companyservicesTransformer->transformCompanyWithServices($company, $allServices);

                return $this->successResponse($itemsOutput);
            }
        }

        return $this->setStatusCode(400)->failureResponse([
            'message' => 'Unable to find Services!'
            ], 'No Services Found !');
    }

    /**
     * Search 
     * 
     * @param Request $request
     * @return array
     */
    public function search(Request $request)
    {
       if($request->has('company_id'))
        {  
            $companyServices = $this->repository->model->where([
                'company_id'=> $request->get('company_id')
            ])->pluck('service_id')->toArray();

            $allServices    = Services::get();
            $itemsOutput    = $this->companyservicesTransformer->transformSearchCompanyWithServices($companyServices, $allServices);

                return $this->successResponse($itemsOutput);
        }

        return $this->setStatusCode(400)->failureResponse([
            'message' => 'Unable to find CompanyServices!'
            ], 'No CompanyServices Found !'); 
    }

    /**
     * Create
     *
     * @param Request $request
     * @return string
     */
    public function create(Request $request)
    {
        if($request->has('company_id') && $request->has('service_id'))
        {
            $isExist = $this->repository->model->where([
                'company_id' => $request->get('company_id'),
                'service_id' => $request->get('service_id'),
            ])->count();

            if(isset($isExist) && $isExist > 0)
            {
                return $this->setStatusCode(400)->failureResponse([
                    'reason' => 'Service Already Added !'
                ], 'Service Already Added!');
            }

            $status = $this->repository->model->create([
                'company_id' => $request->get('company_id'),
                'service_id' => $request->get('service_id')
            ]);

            if($status)
            {
                $responseData = [
                    'message' => 'Service Added successfully'
                ];
                
                return $this->successResponse($responseData, 'Service Added successfully');
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
                $responseData = $this->companyservicesTransformer->transform($itemData);

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
                $responseData   = $this->companyservicesTransformer->transform($itemData);

                return $this->successResponse($responseData, 'CompanyServices is Edited Successfully');
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
                    'success' => 'CompanyServices Deleted'
                ], 'CompanyServices is Deleted Successfully');
            }
        }

        return $this->setStatusCode(404)->failureResponse([
            'reason' => 'Invalid Inputs'
        ], 'Something went wrong !');
    }

    /**
     * Remove
     *
     * @param Request $request
     * @return string
     */
    public function remove(Request $request)
    {
        if($request->has('company_id') && $request->has('service_id'))
        {
            $isExist = $this->repository->model->where([
                'company_id' => $request->get('company_id'),
                'service_id' => $request->get('service_id'),
            ])->count();


            if(isset($isExist) && $isExist == 0)
            {
                return $this->setStatusCode(400)->failureResponse([
                    'reason' => 'No Service Found !'
                ], 'No Service Found!');
            }

            $status = $this->repository->model->where([
                'company_id' => $request->get('company_id'),
                'service_id' => $request->get('service_id')
            ])->delete();

            if($status)
            {
                $responseData = [
                    'message' => 'Service Removed successfully'
                ];
                
                return $this->successResponse($responseData, 'Service Removed successfully');
            }
        }

        return $this->setStatusCode(400)->failureResponse([
            'reason' => 'Invalid Inputs'
            ], 'Something went wrong !');
    }   
}