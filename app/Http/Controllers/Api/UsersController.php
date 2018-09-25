<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests;
use Illuminate\Http\Request;
use App\Models\Access\User\User;
use Response;
use Carbon;
use App\Repositories\Backend\User\UserContract;
use Illuminate\Support\Facades\Validator;
use App\Repositories\Backend\UserNotification\UserNotificationRepositoryContract;
use Illuminate\Foundation\Http\FormRequest;
use App\Http\Transformers\UserTransformer;
use App\Http\Utilities\FileUploads;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuthExceptions\JWTException;
use App\Http\Controllers\Api\BaseApiController;
use App\Events\Backend\Access\User\UserPasswordChanged;
use App\Repositories\Backend\Access\User\UserRepository;
use Auth;
use App\Models\Companies\Companies;
use App\Models\Providers\Providers;
use App\Models\ProviderServices\ProviderServices;
use App\Models\CompanyProviders\CompanyProviders;
use App\Models\Services\Services;
use App\Models\Experiences\Experiences;
use App\Models\ProviderTypes\ProviderTypes;
use App\Models\MasterCategories\MasterCategories;
use App\Models\CompanyServices\CompanyServices;
use Image;

class UsersController extends BaseApiController
{
    protected $userTransformer;
    /**
     * __construct
     * @param UserTransformer                    $userTransformer
     */
    public function __construct(UserTransformer $userTransformer)
    {
        $this->userTransformer = $userTransformer;
    }

    /**
     * Login request
     * 
     * @param Request $request
     * @return type
     */
    public function login(Request $request) 
    {
        $credentials = $request->only('email', 'password');

        try {
            // verify the credentials and create a token for the user
            if (! $token = JWTAuth::attempt($credentials)) {
                return response()->json([
                    'error'     => 'Invalid Credentials',
                    'message'   => 'No User Found for given details',
                    'status'    => false,
                    ], 401);
            }
        } catch (JWTException $e) {
            // something went wrong
            return response()->json([
                    'error'     => 'Somethin Went Wrong!',
                    'message'   => 'Unable to Generate Token!',
                    'status'    => false,
                    ], 500);
        }
        
        if($request->get('device_token') && $request->get('device_type'))
        {
            $user = Auth::user();
            $user->device_type  = $request->get('device_type');
            $user->device_token = $request->get('device_token');
            $user->save();
        }

        $user = Auth::user()->toArray();
        

        $userData = array_merge($user, ['token' => $token]);

        $responseData = $this->userTransformer->transform((object)$userData);

        return $this->successResponse($responseData);
    }

    /**
     * Login request
     * 
     * @param Request $request
     * @return type
     */
    public function socialLogin(Request $request) 
    {
        $validator = Validator::make($request->all(), [
            'social_token'      => 'required',
            'social_provider'   => 'required'
        ]);

        if($validator->fails()) 
        {
            $messageData = '';
            foreach($validator->messages()->toArray() as $message)
            {
                $messageData = $message[0];
            }
            return $this->failureResponse($validator->messages(), $messageData);
        }

       
        $user = User::where([
                'social_provider'   => $request->get('social_provider'),
                'social_token'      => $request->get('social_token')])->first();
        
        if(isset($user) && $user->id)
        {
            Auth::loginUsingId($user->id, true);

            if($request->get('device_token') && $request->get('device_type'))
            {
                $user = Auth::user();
                $user->device_type  = $request->get('device_type');
                $user->device_token = $request->get('device_token');
                $user->save();
            }

            if($request->file('profile_image'))
            {
                $imageName  = rand(11111, 99999) . '_user.' . $request->file('profile_image')->getClientOriginalExtension();
                if(strlen($request->file('profile_image')->getClientOriginalExtension()) > 0)
                {
                    $request->file('profile_image')->move(base_path() . '/public/uploads/user/', $imageName);
                    $user = Auth::user();
                    $user->profile_pic = $imageName;
                    $user->save();
                }
            }

            $user       = Auth::user()->toArray();
            $token      = JWTAuth::fromUser(Auth::user());
            $userData   = array_merge($user, ['token' => $token]);
            $responseData = $this->userTransformer->transform((object)$userData);

            return $this->successResponse($responseData);
        }

        return response()->json([
            'error'     => 'Invalid Credentials',
            'message'   => 'No User Found for given details',
            'status'    => false,
            ], 401);
    }
    
     /**
     * socialCreate
     *
     * @param Request $request
     * @return string
     */
    public function socialCreate(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'social_token'      => 'required',
            'social_provider'   => 'required'
        ]);

        if($validator->fails()) 
        {
            $messageData = '';
            foreach($validator->messages()->toArray() as $message)
            {
                $messageData = $message[0];
            }
            return $this->failureResponse($validator->messages(), $messageData);
        }

        $user = User::where([
                'social_provider'   => $request->get('social_provider'),
                'social_token'      => $request->get('social_token')])->first();
        
        if(isset($user) && $user->id)
        {
            return $this->socialLogin($request);
        }

        
        $validator = Validator::make($request->all(), [
            'email'             => 'required|unique:users|max:255',
            'social_token'      => 'required|unique:users|max:255',
            'social_provider'   => 'required'
        ]);

        if($validator->fails()) 
        {
            $messageData = '';
            foreach($validator->messages()->toArray() as $message)
            {
                $messageData = $message[0];
            }
            return $this->failureResponse($validator->messages(), $messageData);
        }

        $status = $this->socialLogin($request);

        $repository = new UserRepository;
        $input      = $request->all();
        $input      = array_merge($input, [
            'profile_pic'   => 'default.png',
            'user_type'     => 1
        ]);


        if($request->has('profile_pic'))
        {
            $imageName  = "user-".time().".png";
            $path       = base_path() . '/public/uploads/user/' . $imageName;
            Image::make(file_get_contents($request->get('profile_pic')))->save($path); 
            $input = array_merge($input, ['profile_pic' => $imageName]);
        }
       
        $user = $repository->createSocialUserStub($input);
        if($user)
        {
            Auth::loginUsingId($user->id, true);

            $user           = Auth::user()->toArray();
            $token          = JWTAuth::fromUser(Auth::user());
            $userData       = array_merge($user, ['token' => $token]);  
            $responseData   = $this->userTransformer->transform((object)$userData);
            return $this->successResponse($responseData);
        }
        return $this->setStatusCode(400)->failureResponse([
            'reason' => 'Invalid Inputs'
            ], 'Something went wrong !');
    }

    /**
     * Create
     *
     * @param Request $request
     * @return string
     */
    public function create(Request $request)
    {
        $repository = new UserRepository;
        $input      = $request->all();
        $input      = array_merge($input, [
            'profile_pic'   => 'default.png',
            'user_type'     => 1
        ]);
        if($request->file('profile_pic'))
        {
            $imageName  = rand(11111, 99999) . '_user.' . $request->file('profile_pic')->getClientOriginalExtension();
            if(strlen($request->file('profile_pic')->getClientOriginalExtension()) > 0)
            {
                $request->file('profile_pic')->move(base_path() . '/public/uploads/user/', $imageName);
                $input = array_merge($input, ['profile_pic' => $imageName]);
            }
        }
        $validator = Validator::make($request->all(), [
            'email'     => 'required|unique:users|max:255',
            'name'      => 'required',
            'password'  => 'required',
        ]);
        if($validator->fails()) 
        {
            $messageData = '';
            foreach($validator->messages()->toArray() as $message)
            {
                $messageData = $message[0];
            }
            return $this->failureResponse($validator->messages(), $messageData);
        }
        $user = $repository->createUserStub($input);
        if($user)
        {
            Auth::loginUsingId($user->id, true);
            $credentials = [
                'email'     => $input['email'],
                'password'  => $input['password']
            ];
            
            $token          = JWTAuth::attempt($credentials);
            $user           = Auth::user()->toArray();
            $userData       = array_merge($user, ['token' => $token]);  
            $responseData   = $this->userTransformer->transform((object)$userData);
            return $this->successResponse($responseData);
        }
        return $this->setStatusCode(400)->failureResponse([
            'reason' => 'Invalid Inputs'
            ], 'Something went wrong !');
    }

    /**
     * Create
     *
     * @param Request $request
     * @return string
     */
    public function createCompany(Request $request)
    {
        $repository = new UserRepository;
        $input      = $request->all();
        $input      = array_merge($input, [
            'profile_pic'   => 'company_default.png',
            'user_type'     => 3
        ]);
        if($request->file('profile_pic'))
        {
            $imageName  = rand(11111, 99999) . '_user.' . $request->file('profile_pic')->getClientOriginalExtension();
            if(strlen($request->file('profile_pic')->getClientOriginalExtension()) > 0)
            {
                $request->file('profile_pic')->move(base_path() . '/public/uploads/user/', $imageName);
                $input = array_merge($input, ['profile_pic' => $imageName]);
            }
        }
        $validator = Validator::make($request->all(), [
            'email'     => 'required|unique:users|max:255',
            'name'      => 'required',
            'password'  => 'required',
        ]);
        if($validator->fails()) 
        {
            $messageData = '';
            foreach($validator->messages()->toArray() as $message)
            {
                $messageData = $message[0];
            }
            return $this->failureResponse($validator->messages(), $messageData);
        }
        $user = $repository->createUserStub($input);
        if($user)
        {
            Auth::loginUsingId($user->id, true);
            $credentials = [
                'email'     => $input['email'],
                'password'  => $input['password']
            ];
            
            $token          = JWTAuth::attempt($credentials);
            $user           = Auth::user()->toArray();
            $companydata    = [
                'user_id'       => $user['id'],
                'company_name'  => $request->get('name'),
                'start_time'    => $request->get('start_time'),
                'end_time'      => $request->get('end_time'),
            ];
            $company        = Companies::create($companydata);

            if($request->get('services'))
            {
                $services       = explode(',', $request->get('services'));
                $serviceData    = [];

                foreach($services as $service)
                {
                    $serviceData[] = [
                        'company_id'    => $company->id,
                        'service_id'    => $service
                    ];
                }

                CompanyServices::insert($serviceData);
            }

            $userData       = array_merge($user, $companydata, ['token' => $token]);  
            $responseData   = $this->userTransformer->companyTranform((object)$userData);
            return $this->successResponse($responseData);
        }
        return $this->setStatusCode(400)->failureResponse([
            'reason' => 'Invalid Inputs'
            ], 'Something went wrong !');
    }

    /**
     * Create
     *
     * @param Request $request
     * @return string
     */
    public function createProvider(Request $request)
    {
        $repository = new UserRepository;
        $input      = $request->all();
        $input      = array_merge($input, [
            'profile_pic'   => 'default.png',
            'user_type'     => 2
        ]);
        if($request->file('profile_pic'))
        {
            $imageName  = rand(11111, 99999) . '_user.' . $request->file('profile_pic')->getClientOriginalExtension();
            if(strlen($request->file('profile_pic')->getClientOriginalExtension()) > 0)
            {
                $request->file('profile_pic')->move(base_path() . '/public/uploads/user/', $imageName);
                $input = array_merge($input, ['profile_pic' => $imageName]);
            }
        }
        $validator = Validator::make($request->all(), [
            'email'     => 'required|unique:users|max:255',
            'name'      => 'required',
            'password'  => 'required',
        ]);
        if($validator->fails()) 
        {
            $messageData = '';
            foreach($validator->messages()->toArray() as $message)
            {
                $messageData = $message[0];
            }
            return $this->failureResponse($validator->messages(), $messageData);
        }
        $user = $repository->createUserStub($input);
        if($user)
        {
            Auth::loginUsingId($user->id, true);
            $credentials = [
                'email'     => $input['email'],
                'password'  => $input['password']
            ];
            
            $token          = JWTAuth::attempt($credentials);
            $user           = Auth::user()->toArray();
            $providerData   = [
                'user_id'               => $user['id'],
                'level_of_experience'   => $request->get('level_of_experience'),
                'provider_type_id'      => $request->get('provider_type_id'),
                'current_company'       => $request->get('current_company')
            ];


            $provider       = Providers::create($providerData);

            if($request->has('services'))
            {
                $addServices = explode(',',  $request->get('services'));

                foreach($addServices as $service)
                {
                    $providerServiceData[] = [
                        'provider_id'   => $provider->id,
                        'service_id'    => $service
                    ];
                }

                ProviderServices::insert($providerServiceData);
            }

            if($request->has('companies'))
            {
                $addCompanies = explode(',', $request->get('companies'));
                foreach($addCompanies as $company)
                {
                    $providerCompanyData[] = [
                        'provider_id'           => $provider->id,
                        'accept_by_provider'    => 1,
                        'company_id'            => $company
                    ];
                }

                CompanyProviders::insert($providerCompanyData);
            }

            $allServices = Services::all();
            

            $services = ProviderServices::where('provider_id', $user['id'])->with('service')->get();
            
            $companies = CompanyProviders::where('provider_id', $user['id'])->with('company')->get();
            $myProvider = Providers::where('user_id', $user['id'])->with([
                'provider_type'])->first();
            $userData       = array_merge($user, $providerData, ['token' => $token]);  
            $responseData   = $this->userTransformer->providerTranform((object)$userData, (object) $services, (object) $companies, $myProvider, $allServices);
            return $this->successResponse($responseData);
        }
        return $this->setStatusCode(400)->failureResponse([
            'reason' => 'Invalid Inputs'
            ], 'Something went wrong !');
    }

    

    /**
     * Forgot Password
     *
     * @param Request $request
     * @return string
     */
    public function forgotpassword(Request $request)
    {
        if($request->get('email'))
        {
            $userObj = new User;

            $user = $userObj->where('email', $request->get('email'))->first();

            if($user)
            {
                if(1==1) // Send Mail Succes
                {
                    $successResponse = [
                        'message' => 'Reset Password Mail send successfully.'
                    ];
                }

                return $this->successResponse($successResponse);
            }

            return $this->setStatusCode(400)->failureResponse([
                'error' => 'User not Found !'
            ], 'Something went wrong !');
        }

        return $this->setStatusCode(400)->failureResponse([
            'reason' => 'Invalid Inputs'
        ], 'Something went wrong !');
    }

    /**
     * Change Password
     * 
     * @param Request $request
     * @return string
     */
    public function changePassword(Request $request)
    {
        if($request->has('password') && $request->has('old_password'))
        {   
            $userInfo = $this->getAuthenticatedUser();
            $credentials = [
                'email'     => $userInfo->email,
                'password'  => $request->get('old_password')
            ];

            if(! Auth::attempt($credentials))
            {
                return $this->setStatusCode(200)->failureResponse([
                    'reason' => 'Invalid Old Password'
                ], 'Invalid Old Password !');
            }

            $userInfo->password = bcrypt($request->get('password'));

            if ($userInfo->save()) 
            {
                event(new UserPasswordChanged($userInfo));

                $successResponse = [
                    'message' => 'Password Updated successfully.'
                ];
            
                return $this->successResponse($successResponse);
            }
        }

        return $this->setStatusCode(400)->failureResponse([
            'reason' => 'Invalid Inputs'
        ], 'Something went wrong !');
    }

    /**
     * Get User Profile
     * 
     * @param Request $request
     * @return json
     */
    public function getUserProfile(Request $request)
    {
        if($request->get('user_id'))
        {
            $userObj = new User;

            $user = $userObj->find($request->get('user_id'));

            if($user)
            {
                $responseData = $this->userTransformer->transform($user);
                
                return $this->successResponse($responseData);
            }

            return $this->setStatusCode(400)->failureResponse([
                'error' => 'User not Found !'
            ], 'Something went wrong !');
        }

        return $this->setStatusCode(400)->failureResponse([
            'reason' => 'Invalid Inputs'
        ], 'Something went wrong !');     
    }

    /**
     * Update User Profile
     * 
     * @param Request $request
     * @return json
     */
    public function updageUserProfile(Request $request)
    {
        $headerToken = request()->header('Authorization');

        if($headerToken)
        {
            $token      = explode(" ", $headerToken);
            $userToken  = $token[1];
        }
        
        $userInfo   = $this->getApiUserInfo();
        $repository = new UserRepository;
        $input      = $request->all();
        
        if($request->file('profile_pic'))
        {
            $imageName  = rand(11111, 99999) . '_user.' . $request->file('profile_pic')->getClientOriginalExtension();
            if(strlen($request->file('profile_pic')->getClientOriginalExtension()) > 0)
            {
                $request->file('profile_pic')->move(base_path() . '/public/uploads/user/', $imageName);
                $input = array_merge($input, ['profile_pic' => $imageName]);
            }
        }

        $validator = Validator::make($request->all(), [
            'mobile'    => 'required',
            'address'   => 'required',
        ]);

        if($validator->fails()) 
        {
            $messageData = '';

            foreach($validator->messages()->toArray() as $message)
            {
                $messageData = $message[0];
            }
            return $this->failureResponse($validator->messages(), $messageData);
        }

        $status = $repository->updateUserStub($userInfo['userId'], $input);

        if($status)
        {
            return $this->successResponse(['message' => 'Profile Updated Successfully!'], 'Profile Updated Successfully');
        }

        return $this->setStatusCode(400)->failureResponse([
            'reason' => 'Invalid Inputs'
        ], 'Something went wrong !');     
    }

    public function updageUserPassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'password'  => 'required',
        ]);

        if($validator->fails()) 
        {
            $messageData = '';

            foreach($validator->messages()->toArray() as $message)
            {
                $messageData = $message[0];
            }
            return $this->failureResponse($validator->messages(), $messageData);
        }
        
        $userInfo   = $this->getApiUserInfo();
        $user       = User::find($userInfo['userId']);

        $user->password = bcrypt($request->get('password'));

        if ($user->save())
        {
            $successResponse = [
                'message' => 'Password Updated successfully.'
            ];
            
            return $this->successResponse($successResponse);
        }

        return $this->setStatusCode(400)->failureResponse([
            'reason' => 'Invalid Inputs'
        ], 'Something went wrong !');
    }

    /**
     * Logout request
     * 
     * @param  Request $request
     * @return json
     */
    public function logout(Request $request) 
    {
        $userInfo   = $this->getApiUserInfo();
        $user       = User::find($userInfo['userId']);

        $user->device_token = '';

        if($user->save()) 
        {
            $successResponse = [
                'message' => 'User Logged out successfully.'
            ];

            return $this->successResponse($successResponse);
        }

        return $this->setStatusCode(400)->failureResponse([
            'reason' => 'User Not Found !'
        ], 'User Not Found !');
    }

    public function config(Request $request)
    {
        $exps       = Experiences::get();
        $providers  = ProviderTypes::get();
        $categories = MasterCategories::with('services')->get();
        $companies  =    companies::get();
        $catResponse    = [];
        

        foreach($categories as $item)
        {
            $services = [];

            if(isset($item->services))
            {
                foreach($item->services as $service)
                {
                    $services[] = [
                        'id'    => (int) $service->id,
                        'title' => $service->title
                    ];
                }
            }
            $catResponse[] = [
                'id'            => (int) $item->id,
                'title'         => $item->title,
                'description'   => $item->description,
                'services'      => $services
            ];
        }

        $compData = [];
        $expData  = [];
        $providerData = [];
        $serviceData = [];
        $providersData = [];

        foreach($providers as $provider)
        {
            $providersData[] = [
                'provider_type_id'  => (int) $provider->id,
                'title'             => $provider->title
            ];
        }
        foreach($companies as $comp)
        {
            $compData[] = [
                'id'            => (int) $comp->id,
                'company_name'  => $comp->company_name,
                'start_time'    => $comp->start_time,
                'end_time'      => $comp->end_time,
            ];
        }

        foreach($exps as $exp)
        {
            $expData[] = [
                'id'    => (int) $exp->id,
                'exp'   => $exp->level_of_experience,
            ];
        }
        

        $successResponse = [
            'support_number'        => '110001010',
            'privacy_policy_url'    => 'https://www.google.co.in/',
            'terms_conditions_url'  => 'https://www.google.co.in/',
            'about_us_url'          => 'https://www.google.co.in/',
            'min_distance'          => 0,
            'max_distance'          => 10,
            'company_data'          => $compData,
            'experiences_data'      => $expData,
            'services_data'         => $catResponse,
            'provider_types'        => $providersData
        ];

        return $this->successResponse($successResponse);
    }

    /**
     * Update Location
     * 
     * @param Request $request
     * @return json
     */
    public function updateLocation(Request $request)
    {
        if($request->has('lat') && $request->has('long'))
        {
            $userInfo = $this->getAuthenticatedUser();
            $userInfo->lat  = $request->get('lat');
            $userInfo->long = $request->get('long');

            if($userInfo->save())
            {
                $successResponse = [
                        'message' => 'Location Updated successfully.'
                ];
                
                return $this->successResponse($successResponse);
            }
        }

        return $this->setStatusCode(200)->failureResponse([
            'reason' => 'Invalid Inputs'
            ], 'Something went wrong !');
    }

    /**
     * Update Device Token
     * 
     * @param Request $request
     * @return json
     */
    public function updateDeviceToken(Request $request)
    {
        if($request->has('device_token'))
        {
            $userInfo = $this->getAuthenticatedUser();
            $userInfo->device_token  = $request->get('device_token');
            
            if($userInfo->save())
            {
                $successResponse = [
                        'message' => 'Device Token successfully.'
                ];
                
                return $this->successResponse($successResponse);
            }
        }

        return $this->setStatusCode(200)->failureResponse([
            'reason' => 'Invalid Inputs or No data Found'
            ], 'Something went wrong !');
    }
}
