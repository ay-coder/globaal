<?php
namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Transformers\SchedulesTransformer;
use App\Http\Controllers\Api\BaseApiController;
use App\Repositories\Schedules\EloquentSchedulesRepository;
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
        $providerId = $request->has('provider_id') ? $request->get('provider_id') : $userInfo->id;
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
            $allSchedule = $this->repository->model->where([
                'provider_id'   => $request->get('provider_id'),
                'company_id'    => $request->get('company_id'),
            ])->get();
            foreach($days as $day)
            {
                $oldSchedule = $allSchedule->where('service_id',$request->get('service_id'))
                ->where('day_name', access()->getDay($day))
                ->first();

                if(isset($oldSchedule))
                {
                    $date1 = DateTime::createFromFormat('H:i:s', $oldSchedule->start_time)->format('H:i:s');
                    $date2 = DateTime::createFromFormat('H:i', $request->get('start_time'))->format('H:i:s');
                    $date3 = DateTime::createFromFormat('H:i', $request->get('end_time'))->format('H:i:s');

                    if ($date1 >= $date2 && $date1 <= $date3)
                    {
                       continue;
                    }

                    $date4 = DateTime::createFromFormat('H:i', $oldSchedule->start_time)->format('H:i:s');
                    $date5 = DateTime::createFromFormat('H:i', $request->get('start_time'))->format('H:i:s');
                    $date6 = DateTime::createFromFormat('H:i', $request->get('end_time'))->format('H:i:s');
                    
                    if ($date4 >= $date5 && $date4 <= $date6)
                    {
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
                    $message = ['message' => 'Schedule Added Successfully !'];
                    return $this->successResponse($message, 'Schedules is Created Successfully');
                }
            }
        }

        return $this->setStatusCode(400)->failureResponse([
            'reason' => 'Invalid Inputs or Schedule is already Exists !'
            ], 'Invalid Inputs or Schedule is already Exists !');
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
        $itemId = (int) hasher()->decode($request->get($this->primaryKey));

        if($itemId)
        {
            $status = $this->repository->destroy($itemId);

            if($status)
            {
                return $this->successResponse([
                    'success' => 'Schedules Deleted'
                ], 'Schedules is Deleted Successfully');
            }
        }

        return $this->setStatusCode(404)->failureResponse([
            'reason' => 'Invalid Inputs'
        ], 'Something went wrong !');
    }
}