<?php
namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Transformers\TestimonialsTransformer;
use App\Http\Controllers\Api\BaseApiController;
use App\Repositories\Testimonials\EloquentTestimonialsRepository;

class APITestimonialsController extends BaseApiController
{
    /**
     * Testimonials Transformer
     *
     * @var Object
     */
    protected $testimonialsTransformer;

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
    protected $primaryKey = 'testimonialsId';

    /**
     * __construct
     *
     */
    public function __construct()
    {
        $this->repository                       = new EloquentTestimonialsRepository();
        $this->testimonialsTransformer = new TestimonialsTransformer();
    }

    /**
     * List of All Testimonials
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
            $itemsOutput = $this->testimonialsTransformer->transformCollection($items);

            return $this->successResponse($itemsOutput);
        }

        return $this->setStatusCode(400)->failureResponse([
            'message' => 'Unable to find Testimonials!'
            ], 'No Testimonials Found !');
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
        $input      = array_merge($input, [
            'user_id'       => $userInfo->user_id,
            'before_image'   => 'before-default.png',
            'after_image'   => 'after-default.png',
        ]);

        if($request->file('before_image'))
        {
            $imageName  = rand(11111, 99999) . '_testi.' . $request->file('before_image')->getClientOriginalExtension();
            $request->file('before_image')->move(base_path() . '/public/uploads/testimonials/', $imageName);
            $input = array_merge($input, ['before_image' => $imageName]);
        }

        if($request->file('after_image'))
        {
            $imageName  = rand(11111, 99999) . '_testi.' . $request->file('after_image')->getClientOriginalExtension();
            $request->file('after_image')->move(base_path() . '/public/uploads/testimonials/', $imageName);
            $input = array_merge($input, ['after_image' => $imageName]);
        }

        $model = $this->repository->create($input);

        if($model)
        {
            $responseData = $this->testimonialsTransformer->singleTestimonialTransform($model);

            return $this->successResponse($responseData, 'Testimonials is Created Successfully');
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
        if($request->has('testimonial_id'))   
        {
            $model = $this->repository->model->with(['user', 'company', 'provider', 'service'])
            ->where('id', $request->get('testimonial_id'))
            ->first();

            if($model)
            {
                $responseData = $this->testimonialsTransformer->singleTestimonialTransform($model);

                return $this->successResponse($responseData, 'Found Testimonial');
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
        if($request->has('testimonial_id'))
        {
            $input      = $request->all();
            $userInfo   = $this->getAuthenticatedUser();

            $isExist    = $this->repository->model->where([
                'user_id'   => $userInfo->id,
                'id'        => $request->get('testimonial_id')
            ])->first();

            if(!$isExist)
            {
                return $this->setStatusCode(400)->failureResponse([
                    'reason' => "You can't edit other Testimonial"
                ], 'Not allowed to Edit Testimonial!');
            }
            
            if($request->file('before_image'))
            {
                $imageName  = rand(11111, 99999) . '_testi.' . $request->file('before_image')->getClientOriginalExtension();
                $request->file('before_image')->move(base_path() . '/public/uploads/testimonials/', $imageName);
                $input = array_merge($input, ['before_image' => $imageName]);
            }

            if($request->file('after_image'))
            {
                $imageName  = rand(11111, 99999) . '_testi.' . $request->file('after_image')->getClientOriginalExtension();
                $request->file('after_image')->move(base_path() . '/public/uploads/testimonials/', $imageName);
                $input = array_merge($input, ['after_image' => $imageName]);
            }
        }

        $status = $this->repository->update($request->get('testimonial_id'), $input);

        if($status)
        {   
            $model = $this->repository->model->with(['user', 'company', 'provider', 'service'])
            ->where('id', $request->get('testimonial_id'))
            ->first();

            $responseData = $this->testimonialsTransformer->singleTestimonialTransform($model);

            return $this->successResponse($responseData, 'Testimonials is Created Successfully');
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
        if($request->has('testimonial_id'))
        {
            $input      = $request->all();
            $userInfo   = $this->getAuthenticatedUser();

            $testimonial = $this->repository->model->where([
                'user_id'   => $userInfo->id,
                'id'        => $request->get('testimonial_id')
            ])->first();

            if(!$testimonial)
            {
                return $this->setStatusCode(400)->failureResponse([
                    'reason' => "No Testimonial Found !"
                ], 'Not allowed to Delete Testimonial!');
            }

            $status = $testimonial->delete();

            if($status)
            {
                return $this->successResponse([
                    'success' => 'Testimonials Deleted'
                ], 'Testimonials is Deleted Successfully');   
            }
        }
        
        return $this->setStatusCode(404)->failureResponse([
            'reason' => 'Invalid Inputs'
        ], 'Something went wrong !');
    }

    /**
     * Get By Companies
     *
     * @param Request $request
     * @return json
     */
    public function getByCompanies(Request $request)
    {
        if($request->has('company_id'))
        {
            $perPage    = $request->get('per_page') ? $request->get('per_page') : 100;
            $offset     = $request->get('page') ? $request->get('page') : 0;
            $orderBy    = $request->get('orderBy') ? $request->get('orderBy') : 'id';
            $order      = $request->get('order') ? $request->get('order') : 'ASC';
            $items      = $this->repository->model->with(['user', 'company', 'provider', 'service'])
            ->orderBy($orderBy, $order)
            ->where('company_id', $request->get('company_id'))
            ->limit($perPage)
            ->offset($offset)
            ->get();

            if(isset($items) && count($items))
            {
                $itemsOutput = $this->testimonialsTransformer->transformCompanyTestimonials($items);

                return $this->successResponse($itemsOutput);
            }
        }
        
        return $this->setStatusCode(400)->failureResponse([
            'message' => 'Unable to find Testimonials!'
            ], 'No Testimonials Found !');
    }

     /**
     * Get By Companies
     *
     * @param Request $request
     * @return json
     */
    public function getByProvider(Request $request)
    {
        if($request->has('provider_id'))
        {
            $perPage    = $request->get('per_page') ? $request->get('per_page') : 100;
            $offset     = $request->get('page') ? $request->get('page') : 0;
            $orderBy    = $request->get('orderBy') ? $request->get('orderBy') : 'id';
            $order      = $request->get('order') ? $request->get('order') : 'ASC';
            $items      = $this->repository->model->with(['user', 'company', 'provider', 'service'])
            ->orderBy($orderBy, $order)
            ->where('provider_id', $request->get('provider_id'))
            ->limit($perPage)
            ->offset($offset)
            ->get();

            if(isset($items) && count($items))
            {
                $itemsOutput = $this->testimonialsTransformer->transformProviderTestimonials($items);

                return $this->successResponse($itemsOutput);
            }
        }
        
        return $this->setStatusCode(400)->failureResponse([
            'message' => 'Unable to find Testimonials!'
            ], 'No Testimonials Found !');
    }
}