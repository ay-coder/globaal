<?php namespace App\Repositories\Messages;

/**
 * Class EloquentMessagesRepository
 *
 * @author Anuj Jaha ( er.anujjaha@gmail.com)
 */

use App\Models\Messages\Messages;
use App\Repositories\DbRepository;
use App\Exceptions\GeneralException;

class EloquentMessagesRepository extends DbRepository
{
    /**
     * Messages Model
     *
     * @var Object
     */
    public $model;

    /**
     * Messages Title
     *
     * @var string
     */
    public $moduleTitle = 'Messages';

    /**
     * Table Headers
     *
     * @var array
     */
    public $tableHeaders = [
        'id'        => 'Id',
'user_id'        => 'User_id',
'provider_id'        => 'Provider_id',
'patient_id'        => 'Patient_id',
'message'        => 'Message',
'is_read'        => 'Is_read',
'created_at'        => 'Created_at',
'updated_at'        => 'Updated_at',
"actions"         => "Actions"
    ];

    /**
     * Table Columns
     *
     * @var array
     */
    public $tableColumns = [
        'id' =>   [
                'data'          => 'id',
                'name'          => 'id',
                'searchable'    => true,
                'sortable'      => true
            ],
		'user_id' =>   [
                'data'          => 'user_id',
                'name'          => 'user_id',
                'searchable'    => true,
                'sortable'      => true
            ],
		'provider_id' =>   [
                'data'          => 'provider_id',
                'name'          => 'provider_id',
                'searchable'    => true,
                'sortable'      => true
            ],
		'patient_id' =>   [
                'data'          => 'patient_id',
                'name'          => 'patient_id',
                'searchable'    => true,
                'sortable'      => true
            ],
		'message' =>   [
                'data'          => 'message',
                'name'          => 'message',
                'searchable'    => true,
                'sortable'      => true
            ],
		'is_read' =>   [
                'data'          => 'is_read',
                'name'          => 'is_read',
                'searchable'    => true,
                'sortable'      => true
            ],
		'created_at' =>   [
                'data'          => 'created_at',
                'name'          => 'created_at',
                'searchable'    => true,
                'sortable'      => true
            ],
		'updated_at' =>   [
                'data'          => 'updated_at',
                'name'          => 'updated_at',
                'searchable'    => true,
                'sortable'      => true
            ],
		'actions' => [
            'data'          => 'actions',
            'name'          => 'actions',
            'searchable'    => false,
            'sortable'      => false
        ]
    ];

    /**
     * Is Admin
     *
     * @var boolean
     */
    protected $isAdmin = false;

    /**
     * Admin Route Prefix
     *
     * @var string
     */
    public $adminRoutePrefix = 'admin';

    /**
     * Client Route Prefix
     *
     * @var string
     */
    public $clientRoutePrefix = 'frontend';

    /**
     * Admin View Prefix
     *
     * @var string
     */
    public $adminViewPrefix = 'backend';

    /**
     * Client View Prefix
     *
     * @var string
     */
    public $clientViewPrefix = 'frontend';

    /**
     * Module Routes
     *
     * @var array
     */
    public $moduleRoutes = [
        'listRoute'     => 'messages.index',
        'createRoute'   => 'messages.create',
        'storeRoute'    => 'messages.store',
        'editRoute'     => 'messages.edit',
        'updateRoute'   => 'messages.update',
        'deleteRoute'   => 'messages.destroy',
        'dataRoute'     => 'messages.get-list-data'
    ];

    /**
     * Module Views
     *
     * @var array
     */
    public $moduleViews = [
        'listView'      => 'messages.index',
        'createView'    => 'messages.create',
        'editView'      => 'messages.edit',
        'deleteView'    => 'messages.destroy',
    ];

    /**
     * Construct
     *
     */
    public function __construct()
    {
        $this->model = new Messages;
    }

    /**
     * Create Messages
     *
     * @param array $input
     * @return mixed
     */
    public function create($input)
    {
        $input = $this->prepareInputData($input, true);
        $model = $this->model->create($input);

        if($model)
        {
            return $model;
        }

        return false;
    }

    /**
     * Update Messages
     *
     * @param int $id
     * @param array $input
     * @return bool|int|mixed
     */
    public function update($id, $input)
    {
        $model = $this->model->find($id);

        if($model)
        {
            $input = $this->prepareInputData($input);

            return $model->update($input);
        }

        return false;
    }

    /**
     * Destroy Messages
     *
     * @param int $id
     * @return mixed
     * @throws GeneralException
     */
    public function destroy($id)
    {
        $model = $this->model->find($id);

        if($model)
        {
            return $model->delete();
        }

        return  false;
    }

    /**
     * Get All
     *
     * @param string $orderBy
     * @param string $sort
     * @return mixed
     */
    public function getAll($providerId = null, $patientId = null)
    {
        if($providerId && $patientId)
        {
            return $this->model->where([
                'provider_id'   => $providerId,
                'patient_id'    => $patientId
            ])
            ->with([
                'patient',
                'provider'
            ])
            ->get();
        }

        return false;
    }

    /**
     * Get All
     *
     * @param string $orderBy
     * @param string $sort
     * @return mixed
     */
    public function getAllChat($providerId = null, $patientId = null)
    {
        if($providerId && $patientId)
        {
            return $this->model->where([
                'provider_id'   => $providerId,
                'patient_id'    => $patientId
            ])->orWhere([
                'provider_id'   => $patientId,
                'patient_id'    => $providerId
            ])
            ->with([
                'patient',
                'provider'
            ])
            ->get();
        }

        return false;
    }
    
    
    /**
     * Get All User Messages
     * 
     * @var int
     */
    public function getAllUserMessages($userId = null)
    {
        if($userId)
        {
            $messages = $this->model->where([
                'patient_id' => $userId
            ])
            ->with([
                'patient',
                'provider',
                'provider.user'
            ])
            ->orderBy('id', 'desc')
            ->get();

            $response       = [];
            $providerId     = [];

            foreach($messages as $message)
            {
                if(in_array($message->provider_id, $providerId))
                {
                    continue;   
                }

                $providerId[]   = $message->provider_id;
                $response[]     = $message;
            }

            return $response;
        }
        
        return false;
    }

    /**
     * Get ALl Provider Messages
     * 
     * @var [type]
     */
    public function getAllProviderMessages($userId = null)
    {
        if($userId)
        {
            $providerId = access()->getProviderId($userId);
            $messages = $this->model->where([
                'provider_id' => $providerId
            ])
            ->with([
                'patient',
                'provider',
                'provider.user'
            ])
            ->orderBy('id', 'desc')
            ->get();

            $response      = [];
            $patientId     = [];

            foreach($messages as $message)
            {
                if(in_array($message->patient_id, $patientId))
                {
                    continue;   
                }

                $patientId[]    = $message->patient_id;
                $response[]     = $message;
            }

            return $response;
        }
        
        return false;
    }

    /**
     * Get by Id
     *
     * @param int $id
     * @return mixed
     */
    public function getById($id = null)
    {
        if($id)
        {
            return $this->model->find($id);
        }

        return false;
    }

    /**
     * Get Table Fields
     *
     * @return array
     */
    public function getTableFields()
    {
        return [
            $this->model->getTable().'.*'
        ];
    }

    /**
     * @return mixed
     */
    public function getForDataTable()
    {
        return $this->model->select($this->getTableFields())->get();
    }

    /**
     * Set Admin
     *
     * @param boolean $isAdmin [description]
     */
    public function setAdmin($isAdmin = false)
    {
        $this->isAdmin = $isAdmin;

        return $this;
    }

    /**
     * Prepare Input Data
     *
     * @param array $input
     * @param bool $isCreate
     * @return array
     */
    public function prepareInputData($input = array(), $isCreate = false)
    {
        if($isCreate)
        {
            $input = array_merge($input, ['user_id' => access()->user()->id]);
        }

        return $input;
    }

    /**
     * Get Table Headers
     *
     * @return string
     */
    public function getTableHeaders()
    {
        if($this->isAdmin)
        {
            return json_encode($this->setTableStructure($this->tableHeaders));
        }

        $clientHeaders = $this->tableHeaders;

        unset($clientHeaders['username']);

        return json_encode($this->setTableStructure($clientHeaders));
    }

    /**
     * Get Table Columns
     *
     * @return string
     */
    public function getTableColumns()
    {
        if($this->isAdmin)
        {
            return json_encode($this->setTableStructure($this->tableColumns));
        }

        $clientColumns = $this->tableColumns;

        unset($clientColumns['username']);

        return json_encode($this->setTableStructure($clientColumns));
    }
}