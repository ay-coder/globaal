<?php
namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Transformers\SchedulesTransformer;
use App\Http\Controllers\Api\BaseApiController;
use App\Repositories\Schedules\EloquentSchedulesRepository;
use App\Models\Providers\Providers;
use App\Models\Companies\Companies;
use DateTime;

class APISchedulesController extends BaseApiController
{
    /**
     * Schedules Transformer
     *
     * @var Object
     */
    protected $schedulesTransformer;

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
    protected $primaryKey = 'schedulesId';

    /**
     * __construct
     *
     */
    public function __construct()
    {
        $this->repository                       = new EloquentSchedulesRepository();
        $this->schedulesTransformer = new SchedulesTransformer();
    }

    /**
     * List of All Schedules
     *
     * @param Request $request
     * @return json
     */
    public function index(Request $request)
    {
        $userInfo   = $this->getAuthenticatedUser();
        $providerId = $request->has('provider_id') ? $request->get('provider_id') : access()->getProviderId($userInfo->id);
        $items      = $this->repository->model->with([
            'provider', 'service', 'user', 'company', 'provider.user'
        ])->where('provider_id', $providerId)->get();

        if(isset($items) && count($items))
        {
            $itemsOutput = $this->schedulesTransformer->transformProviderSchedules($items);

            return $this->successResponse($itemsOutput);
        }

        return $this->setStatusCode(400)->failureResponse([
            'message' => 'Unable to find Schedules!'
            ], 'No Schedules Found !');
    }

    /**
     * List of All Schedules
     *
     * @param Request $request
     * @return json
     */
    public function filter(Request $request)
    {
        if($request->has('company_id') && $request->has('service_id'))
        {
            $conditions = [
                'service_id' => $request->get('service_id'),
                'company_id' => $request->get('company_id')
            ];

            if($request->has('provider_id'))
            {
                $conditions = array_merge($conditions, [
                    'provider_id' => $request->get('provider_id')
                ]);
            }

            $items = $this->repository->model->with([
                'provider', 'service', 'user', 'company', 'provider.user'
            ])->where($conditions)->get();

            if(isset($items) && count($items))
            {
                $itemsOutput = $this->schedulesTransformer->transformProviderSchedules($items);

                return $this->successResponse($itemsOutput);
            }
        }
       


        return $this->setStatusCode(400)->failureResponse([
            'message' => 'Unable to find Schedules!'
            ], 'No Schedules Found !');
    }

    /**
     * Create
     *
     * @param Request $request
     * @return string
     */
    public function create(Request $request)
    {
        if($request->has('provider_id') && $request->has('days') && $request->has('company_id') && $request->has('service_id'))
        {
            $days       = explode(',', $request->get('days'));
            $input      = $request->all();
            $userInfo   = $this->getAuthenticatedUser();
            $input      = array_merge($input, ['user_id' => $userInfo->id]);
            $createData = [];
            $skippDays  = [];
            $allSchedule = $this->repository->model->where([
                'provider_id'   => $request->get('provider_id'),
                'company_id'    => $request->get('company_id'),
            ])->get();
            foreach($days as $day)
            {
                $oldSchedule = $allSchedule->where('service_id',$request->get('service_id'))
                ->where('day_name', access()->getDay($day))
                ->where('provider_id', $request->get('provider_id'))
                ->where('company_id', $request->get('company_id'))
                ->first();

                if(isset($oldSchedule) && isset($oldSchedule->start_time) && strlen(
                    $oldSchedule->start_time) > 2)
                {
                    $startTime = DateTime::createFromFormat('H:i', $request->get('start_time'))->format('H:i:s');
                    $endTime = DateTime::createFromFormat('H:i', $request->get('end_time'))->format('H:i:s');


                    $query = $this->repository->model->where([
                        'provider_id'   => $request->get('provider_id'),
                        'company_id'    => $request->get('company_id'),
                    ])
                    ->where('service_id',$request->get('service_id'))
                    ->where('day_name', access()->getDay($day))
                    ->where('provider_id', $request->get('provider_id'))
                    ->where('company_id', $request->get('company_id'));
                    
                    if($startTime)
                    {
                        $query->whereBetween('start_time',  [$startTime, $endTime])
                        ->orWhereBetween('end_time',  [$startTime, $endTime]);
                    }


                    $timeAllow = $query->get();

                    if(isset($timeAllow) && count($timeAllow))
                    {
                        $skippDays[] = access()->getDay($day);
                        continue;
                    }
                }

                $createData[] = [
                    'user_id'       => $userInfo->id,
                    'provider_id'   => $request->get('provider_id'),
                    'service_id'    => $request->get('service_id'),
                    'company_id'    => $request->get('company_id'),
                    'start_time'    => $request->get('start_time'),
                    'end_time'      => $request->get('end_time'),
                    'day_name'      => access()->getDay($day)
                ];
            }

            if(isset($createData) && count($createData))
            {
                $status = $this->repository->model->insert($createData);
                
                if($status)
                {
                    $companyInfo = Companies::where('id', $request->get('company_id'))->with('user')->first();
                    $provider   = Providers::with('user')->where('id', $request->get('provider_id'))->first();
                    $text       = $companyInfo->company_name . ' has created a schedule for you'; 

                    $payload    = [
                                'mtitle'        => '',
                                'mdesc'         => $text,
                                'provider_id'   => $request->get('provider_id'),
                                'company_id'    => $request->get('company_id'),
                                'ntype'         => 'NEW_SCHEDULE_CREATED'
                    ];

                    $storeNotification = [
                        'user_id'       => $provider->user->id,
                        'title'         => $text,
                        'service_id'    => $request->get('service_id'),
                        'provider_id'   => $request->get('provider_id'),
                        'company_id'    => $request->get('company_id'),
                        'notification_type' => 'NEW_SCHEDULE_CREATED'
                    ];

                   

                    // Add Notification
                    access()->addNotification($storeNotification);
                    
                    // Push Notification
                    access()->sentPushNotification($provider->user, $payload);
                        

                    $skipDaysMsg = '';
                    if(count($skippDays))
                    {
                        $skipDaysMsg = implode(', ', $skippDays);
                    }

                    $message = [
                        'message'   => 'Schedule Added Successfully !',
                        'skippDays' => $skipDaysMsg
                    ];
                    return $this->successResponse($message, 'Schedules is Created Successfully');
                }
            }
        }

        return $this->setStatusCode(400)->failureResponse([
            'reason' => 'Invalid Inputs or Schedule is already Exists !'
            ], 'Invalid Inputs or Schedule is already Exists !');
    }

    public function getProvideSchedule(Request $request)
    {
        if($request->has('provider_id'))
        {
            $conditions = [
                'provider_id' => $request->get('provider_id')
            ];
            
            $items = $this->repository->model->with([
                'provider', 'service', 'user', 'company', 'provider.user'
            ])->where($conditions)->get();

            if(isset($items) && count($items))
            {
                $itemsOutput = $this->schedulesTransformer->transformProviderSchedules($items);

                return $this->successResponse($itemsOutput);
            }
        }

        return $this->setStatusCode(400)->failureResponse([
            'reason' => 'No Provider found!'
            ], 'No Provider found!');
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
                $responseData = $this->schedulesTransformer->transform($itemData);

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
                $responseData   = $this->schedulesTransformer->transform($itemData);

                return $this->successResponse($responseData, 'Schedules is Edited Successfully');
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
        if($request->has('schedule_id') && $request->has('company_id') && $request->has('provider_id'))
        {
            $model = $this->repository->model->where([
                'id'            => $request->get('schedule_id'),
                'provider_id'   => $request->get('provider_id'),
                'company_id'    => $request->get('company_id'),
            ])->first();

            if(isset($model) && isset($model->id))
            {
                if($model->delete())
                {
                    return $this->successResponse([
                        'success' => 'Schedules Deleted'
                    ], 'Schedules is Deleted Successfully');
                }
            }
        }
       
        return $this->setStatusCode(404)->failureResponse([
            'reason' => 'Invalid Inputs or Schedule Not Found !'
        ], 'Invalid Inputs or Schedule Not Found !');
    }
}