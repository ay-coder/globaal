<?php
namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Transformers\CredentialsTransformer;
use App\Http\Controllers\Api\BaseApiController;
use App\Repositories\Credentials\EloquentCredentialsRepository;

class APICredentialsController extends BaseApiController
{
    /**
     * Credentials Transformer
     *
     * @var Object
     */
    protected $credentialsTransformer;

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
    protected $primaryKey = 'credentialsId';

    /**
     * __construct
     *
     */
    public function __construct()
    {
        $this->repository                       = new EloquentCredentialsRepository();
        $this->credentialsTransformer = new CredentialsTransformer();
    }

    /**
     * List of All Credentials
     *
     * @param Request $request
     * @return json
     */
    public function index(Request $request)
    {
        $userInfo   = $this->getAuthenticatedUser();
        $providerId = access()->getProviderId($userInfo->id);
        $items      = $this->repository->model->with(['provider', 'provider.user'])->where('provider_id', $providerId)->get();

        if(isset($items) && count($items))
        {
            $itemsOutput = $this->credentialsTransformer->showAllCredentialTranform($items);

            return $this->successResponse($itemsOutput);
        }

        return $this->setStatusCode(400)->failureResponse([
            'message' => 'Unable to find Credentials!'
            ], 'No Credentials Found !');
    }

    /**
     * Create
     *
     * @param Request $request
     * @return string
     */
    public function create(Request $request)
    {
        $input      = $request->all();
        $userInfo   = $this->getAuthenticatedUser();
        $providerId = access()->getProviderId($userInfo->id);
        $input      = array_merge($input, ['provider_id' => $providerId]);

        if($request->file('image'))
        {
            $imageName  = rand(11111, 99999) . '_credential.' . $request->file('image')->getClientOriginalExtension();
            $request->file('image')->move(base_path() . '/public/uploads/credentials/', $imageName);
            $input = array_merge($input, ['image' => $imageName]);
        }

        $model      = $this->repository->create($input);

        if($model)
        {
            return $this->successResponse(['message' => 'Added Credential Successfully!'], 'Credentials is Created Successfully');
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
                $responseData = $this->credentialsTransformer->transform($itemData);

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
                $responseData   = $this->credentialsTransformer->transform($itemData);

                return $this->successResponse($responseData, 'Credentials is Edited Successfully');
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
        if($request->has('credential_id'))
        {
            $credentialId   = $request->get('credential_id');
            $userInfo       = $this->getAuthenticatedUser();
            $providerId     = access()->getProviderId($userInfo->id);
            $credential     = $this->repository->model->where([
                'id'            => $credentialId,
                'provider_id'   => $providerId
            ])->first();

            if($credential)
            {
                if($credential->delete())
                {
                    return $this->successResponse([
                        'success' => 'Credentials Deleted'
                    ], 'Credentials is Deleted Successfully');
                }
            }
        }
        
        return $this->setStatusCode(404)->failureResponse([
            'reason' => 'Invalid Inputs'
        ], 'Something went wrong !');
    }
}