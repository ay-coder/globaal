<?php
namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Transformers\ProvidersTransformer;
use App\Http\Controllers\Api\BaseApiController;
use App\Repositories\Providers\EloquentProvidersRepository;

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
        $items     = $this->repository->model->with(['services', 'services.service', 'user', 'leavelOfExperience', 'company'])
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
        $itemId = (int) hasher()->decode($request->get($this->primaryKey));

        if($itemId)
        {
            $itemData = $this->repository->getById($itemId);

            if($itemData)
            {
                $responseData = $this->providersTransformer->transform($itemData);

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
}