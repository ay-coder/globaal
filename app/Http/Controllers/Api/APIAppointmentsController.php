<?php
namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Transformers\AppointmentsTransformer;
use App\Http\Controllers\Api\BaseApiController;
use App\Models\Access\User\User;
use App\Models\Providers\Providers;
use App\Models\Companies\Companies;
use App\Repositories\Appointments\EloquentAppointmentsRepository;
use DateTime;

class APIAppointmentsController extends BaseApiController
{
    /**
     * Appointments Transformer
     *
     * @var Object
     */
    protected $appointmentsTransformer;

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
    protected $primaryKey = 'appointmentsId';

    /**
     * __construct
     *
     */
    public function __construct()
    {
        $this->repository                       = new EloquentAppointmentsRepository();
        $this->appointmentsTransformer = new AppointmentsTransformer();
    }

    /**
     * List of All Appointments
     *
     * @param Request $request
     * @return json
     */
    public function index(Request $request)
    {
        $userInfo   = $this->getAuthenticatedUser();
        $perPage    = $request->get('per_page') ? $request->get('per_page') : 100;
        $offset     = $request->get('page') ? $request->get('page') : 0;
        $items      = $this->repository->model->with([
            'service', 'user', 'provider', 'provider.user', 'company', 'company.user'
        ])->where([
            'user_id' => $userInfo->id,
        ])
        ->whereNotIn('current_status', ['CANCELED'])
        ->orderBy('booking_date')
        ->limit($perPage)
        ->offset($offset)
        ->get();

        if(isset($items) && count($items))
        {
            $itemsOutput = $this->appointmentsTransformer->showAllAppointments($items);

            return $this->successResponse($itemsOutput);
        }

        return $this->setStatusCode(400)->failureResponse([
            'message' => 'Unable to find Appointments!'
            ], 'No Appointments Found !');
    }

    /**
     * Get Past Appointments
     *
     * @param Request $request
     * @return json
     */
    public function getPastData(Request $request)
    {
        $userInfo   = $this->getAuthenticatedUser();
        $perPage    = $request->get('per_page') ? $request->get('per_page') : 100;
        $offset     = $request->get('page') ? $request->get('page') : 0;
        $items      = $this->repository->model->with([
            'service', 'user', 'provider', 'company', 'company.user'
        ])->where([
            'user_id'   => $userInfo->id,
        ])
        ->whereIn('current_status', ['CANCELED', 'COMPLETED'])
        ->orderBy('booking_date', 'DESC')
        ->limit($perPage)
        ->offset($offset)
        ->get();

        if(isset($items) && count($items))
        {
            $itemsOutput = $this->appointmentsTransformer->showAllAppointments($items);

            return $this->successResponse($itemsOutput);
        }

        return $this->setStatusCode(400)->failureResponse([
            'message' => 'Unable to find Appointments!'
            ], 'No Appointments Found !');
    }
    

    /**
     * Create
     *
     * @param Request $request
     * @return string
     */
    public function create(Request $request)
    {
        if($request->has('company_id') && $request->has('provider_id')  && $request->has('service_id') && $request->has('booking_date') && $request->has('start_time') && $request->has('end_time'))
        {
            $date       = date('Y-m-d');
            $userInfo   = $this->getAuthenticatedUser();
            $bookings   = $this->repository->model->where([
                'provider_id'   => $request->get('provider_id'),
                'service_id'    => $request->get('service_id'),
                'company_id'    => $request->get('company_id'),
                'booking_date'  => $request->get('booking_date'),
            ])
            ->where('status', '!=', 'CANCELED')
            ->get();

            if(isset($bookings) && count($bookings))
            {
                foreach($bookings as $booking)
                {
                    $startTime   = strtotime($date.$booking->start_time);
                    $actualStart = strtotime($date.$request->get('start_time'));
                    $actualEnd   = strtotime($date.$request->get('end_time'));

                    $startTime = DateTime::createFromFormat('H:i', $request->get('start_time'))->format('H:i:s');
                    $endTime = DateTime::createFromFormat('H:i', $request->get('end_time'))->format('H:i:s');

                    $timeAllow = $this->repository->model->where([
                        'provider_id'   => $request->get('provider_id'),
                        'service_id'    => $request->get('service_id'),
                        'company_id'    => $request->get('company_id'),
                        'booking_date'  => $request->get('booking_date'),
                    ])
                    ->where('current_status', '!=', 'CANCELED')
                    ->where('start_time', '>=', $startTime)
                    ->where('end_time', '<=', $endTime)
                    ->get();

                    if(isset($timeAllow) && count($timeAllow))
                    {
                        return $this->setStatusCode(400)->failureResponse([
                            'reason' => 'Some one already booked this Schedule Please change booking time and try.'
                            ], 'Some one already booked this Schedule Please change booking time and try.');
                    }
                }
            }
            
            $status = $this->repository->model->create([
                'user_id'       => $userInfo->id,
                'provider_id'   => $request->get('provider_id'),
                'service_id'    => $request->get('service_id'),
                'company_id'    => $request->get('company_id'),
                'booking_date'  => $request->get('booking_date'),
                'start_time'    => $request->get('start_time'),
                'end_time'      => $request->get('end_time')
            ]);

            if($status)
            {
                $provider   = Providers::with('user')->where('id', $request->get('provider_id'))->first();
                $companyInfo = Companies::where('id', $request->get('company_id'))->with('user')->first();
                $text        = $userInfo->name . ' has booked an appointment for ' . $request->get('booking_date') . ' ' . $request->get('start_time') . ' To '. $request->get('end_time') . '.'; 

                $companyText = $userInfo->name . ' has booked an appointment for ' . $request->get('booking_date') . ' ' .  $request->get('start_time') . ' To '.$request->get('end_time') . ' with '.  $provider->user->name;

                $payload    = [
                            'mtitle'        => '',
                            'mdesc'         => $text,
                            'provider_id'   => $request->get('provider_id'),
                            'company_id'    => $request->get('company_id'),
                            'ntype'         => 'NEW_APPOINTMENT_BOOKED'
                ];

                $companyPayload    = [
                            'mtitle'        => '',
                            'mdesc'         => $companyText,
                            'provider_id'   => $request->get('provider_id'),
                            'company_id'    => $request->get('company_id'),
                            'ntype'         => 'NEW_APPOINTMENT_BOOKED'
                ];

                $storeNotification = [
                    'user_id'       => $provider->user->id,
                    'title'         => $text,
                    'service_id'    => $request->get('service_id'),
                    'provider_id'   => $request->get('provider_id'),
                    'company_id'    => $request->get('company_id'),
                    'patient_id'    => $userInfo->id,
                    'notification_type' => 'NEW_APPOINTMENT_BOOKED'
                ];

                $storeCompanyNotification = [
                    'user_id'       => $companyInfo->user->id,
                    'title'         => $companyText,
                    'provider_id'   => $request->get('provider_id'),
                    'service_id'    => $request->get('service_id'),
                    'company_id'    => $request->get('company_id'),
                    'patient_id'    => $userInfo->id,
                    'notification_type' => 'NEW_APPOINTMENT_BOOKED'
                ];

                // Add Notification
                access()->addNotification($storeNotification);
                access()->addNotification($storeCompanyNotification);

                // Push Notification
                access()->sentPushNotification($provider->user, $payload);
                access()->sentPushNotification($companyInfo->user, $companyPayload);

                $responseData = [
                    'message' => 'Appointments is Created Successfully'
                ];
                return $this->successResponse($responseData, 'Appointments is Created Successfully');
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
                $responseData = $this->appointmentsTransformer->transform($itemData);

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
                $responseData   = $this->appointmentsTransformer->transform($itemData);

                return $this->successResponse($responseData, 'Appointments is Edited Successfully');
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
        if($request->has('appointment_id'))
        {
            $userInfo   = $this->getAuthenticatedUser();
            $isExist    = $this->repository->model->where([
                'id'        => $request->get('appointment_id'),
                'user_id'   => $userInfo->id
            ])
            ->first();

            if(isset($isExist) && count($isExist))
            {
                if($isExist->delete())
                {
                    $responseData = [
                        'message' => 'Appointments deleted Successfully'
                    ];
                return $this->successResponse($responseData, 'Appointments deleted Successfully');
                }
            }
        }

        return $this->setStatusCode(404)->failureResponse([
            'reason' => 'Invalid Inputs or No Appointment found'
        ], 'Invalid Inputs or No Appointment found');
    }

    /**
     * Delete
     *
     * @param Request $request
     * @return string
     */
    public function cancel(Request $request)
    {
        if($request->has('appointment_id'))
        {
            $userInfo   = $this->getAuthenticatedUser();
            $isExist    = $this->repository->model->where([
                'id'        => $request->get('appointment_id'),
                'user_id'   => $userInfo->id
            ])
            ->first();

            if(isset($isExist) && count($isExist))
            {
                $isExist->current_status = 'CANCELED';
                if($isExist->save())
                {
                    $responseData = [
                        'message' => 'Appointments cancelled Successfully'
                    ];
                return $this->successResponse($responseData, 'Appointments cancelled Successfully');
                }
            }
        }
        
        return $this->setStatusCode(404)->failureResponse([
            'reason' => 'Invalid Inputs'
        ], 'Something went wrong !');
    }
}