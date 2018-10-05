<?php
namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Transformers\MessagesTransformer;
use App\Http\Controllers\Api\BaseApiController;
use App\Repositories\Messages\EloquentMessagesRepository;
use App\Models\Access\User\User;
use App\Models\Providers\Providers;

class APIMessagesController extends BaseApiController
{
    /**
     * Messages Transformer
     *
     * @var Object
     */
    protected $messagesTransformer;

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
    protected $primaryKey = 'messagesId';

    /**
     * __construct
     *
     */
    public function __construct()
    {
        $this->repository                       = new EloquentMessagesRepository();
        $this->messagesTransformer = new MessagesTransformer();
    }

    /**
     * List of All Messages
     *
     * @param Request $request
     * @return json
     */
    public function index(Request $request)
    {
        $userInfo   = $this->getAuthenticatedUser();
        
        if($userInfo->user_type == 1)
        {
            $messages = $this->repository->getAllUserMessages($userInfo->id);
        }
        else
        {
            $providerId = access()->getProviderId($userInfo->id);
            $messages = $this->repository->getAllProviderMessages($providerId);   
        }

        if($messages && count($messages))
        {
            $itemsOutput = $this->messagesTransformer->messageTranform($messages);

            return $this->successResponse($itemsOutput);
        }
        
        return $this->setStatusCode(400)->failureResponse([
            'message' => 'Unable to find Messages!'
            ], 'No Messages Found !');
    }

    /**
     * List of All Messages
     *
     * @param Request $request
     * @return json
     */
    public function getChat(Request $request)
    {
        if($request->has('provider_id') && $request->has('patient_id'))
        {
            $messages = $this->repository->getAllChat($request->get('provider_id'), $request->get('patient_id'));   
            
            if($messages && count($messages))
            {
                $itemsOutput = $this->messagesTransformer->messageTranform($messages);

                return $this->successResponse($itemsOutput);
            }
        }

        if($request->has('patient_id'))
        {
            $chatUser = User::where('id', $request->get('patient_id'))->first();
        }
        else
        {
            $chatProvider = Providers::with('user')->where('id', $request->get('provider_id'))->first();   
            $chatUser     = $chatProvider->user;
        }
        $chatMsg = 'Start a conversation with ' . $chatUser->name;

        
        return $this->setStatusCode(400)->failureResponse([
            'message' => $chatMsg
            ], $chatMsg);
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
        $providerId = $request->get('provider_id');
        $patientId  = $request->get('patient_id');
        $input      = array_merge($input, ['user_id' => access()->user()->id]);

        
        $model = $this->repository->create($input);

        if($model)
        {
            $text       = $userInfo->name . ' has messaged you';
            $payload    = [
                    'mtitle'        => '',
                    'mdesc'         => $text,
                    'message_id'    => $model->id,
                    'provider_id'   => $providerId,
                    'patient_id'    => $patientId,
                    'ntype'         => 'NEW_MESSAGE'
            ];

            if($patientId == access()->user()->id)
            {
                $provider = Providers::with('user')->where('id', $providerId)->first();
                $user     = $provider->user;
            }
            else
            {
                $user = User::where('id', $patientId)->first();
            }

            $storeNotification = [
                'user_id'           => $user->id,
                'title'             => $text,
                'message_id'        => $model->id,
                'notification_type' => 'NEW_MESSAGE'
            ];

            // Add Notification
            access()->addNotification($storeNotification);

            // Push Notification
            access()->sentPushNotification($user, $payload);

            $responseData = $this->messagesTransformer->singleMessageTranform($model);
            return $this->successResponse($responseData);
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
                $responseData = $this->messagesTransformer->transform($itemData);

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
                $responseData   = $this->messagesTransformer->transform($itemData);

                return $this->successResponse($responseData, 'Messages is Edited Successfully');
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
                    'success' => 'Messages Deleted'
                ], 'Messages is Deleted Successfully');
            }
        }

        return $this->setStatusCode(404)->failureResponse([
            'reason' => 'Invalid Inputs'
        ], 'Something went wrong !');
    }
}