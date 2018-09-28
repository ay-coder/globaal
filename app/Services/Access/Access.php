<?php

namespace App\Services\Access;

use Illuminate\Contracts\Auth\Authenticatable;
use App\Models\Providers\Providers;
use App\Models\Companies\Companies;
use App\Models\CompanyProviders\CompanyProviders;
use App\Models\Notifications\Notifications;
use App\Library\Push\PushNotification;

/**
 * Class Access.
 */
class Access
{
    /**
     * Get the currently authenticated user or null.
     */
    public function user()
    {
        return auth()->user();
    }

    /**
     * Return if the current session user is a guest or not.
     *
     * @return mixed
     */
    public function guest()
    {
        return auth()->guest();
    }

    /**
     * @return mixed
     */
    public function logout()
    {
        return auth()->logout();
    }

    /**
     * Get the currently authenticated user's id.
     *
     * @return mixed
     */
    public function id()
    {
        return auth()->id();
    }

    /**
     * @param Authenticatable $user
     * @param bool            $remember
     */
    public function login(Authenticatable $user, $remember = false)
    {
        return auth()->login($user, $remember);
    }

    /**
     * @param $id
     *
     * @return mixed
     */
    public function loginUsingId($id)
    {
        return auth()->loginUsingId($id);
    }

    /**
     * Checks if the current user has a Role by its name or id.
     *
     * @param string $role Role name.
     *
     * @return bool
     */
    public function hasRole($role)
    {
        if ($user = $this->user()) {
            return $user->hasRole($role);
        }

        return false;
    }

    /**
     * Checks if the user has either one or more, or all of an array of roles.
     *
     * @param  $roles
     * @param bool $needsAll
     *
     * @return bool
     */
    public function hasRoles($roles, $needsAll = false)
    {
        if ($user = $this->user()) {
            return $user->hasRoles($roles, $needsAll);
        }

        return false;
    }

    /**
     * Check if the current user has a permission by its name or id.
     *
     * @param string $permission Permission name or id.
     *
     * @return bool
     */
    public function allow($permission)
    {
        if ($user = $this->user()) {
            return $user->allow($permission);
        }

        return false;
    }

    /**
     * Check an array of permissions and whether or not all are required to continue.
     *
     * @param  $permissions
     * @param  $needsAll
     *
     * @return bool
     */
    public function allowMultiple($permissions, $needsAll = false)
    {
        if ($user = $this->user()) {
            return $user->allowMultiple($permissions, $needsAll);
        }

        return false;
    }

    /**
     * @param  $permission
     *
     * @return bool
     */
    public function hasPermission($permission)
    {
        return $this->allow($permission);
    }

    /**
     * @param  $permissions
     * @param  $needsAll
     *
     * @return bool
     */
    public function hasPermissions($permissions, $needsAll = false)
    {
        return $this->allowMultiple($permissions, $needsAll);
    }

    /**
     * Get Notification Count
     * 
     * @param int $userId
     * @return int
     */
    public function getUserUnreadNotificationCount($userId = null)
    {
       return 0;
    }

    /**
     * GetDay
     * 
     * @param  integer $day [description]
     * @return [type]       [description]
     */
    public function getDay($day = 0)
    {
        $days = [
            'Monday',
            'Tuesday',
            'Wednesday',
            'Thursday',
            'Friday',
            'Saturday',
            'Sunday',
        ];

        return $days[$day];
    }

    /**
     * Get ProviderId
     * 
     * @param int $userId
     * @return int
     */
    public function getCompanyId($userId)
    {
        if($userId)
        {
            $company = Companies::where('user_id', $userId)->first();

            if(isset($company) && isset($company->id))
            {
                return $company->id;
            }
        }

        return '';
    }

    /**
     * Get ProviderId
     * 
     * @param int $userId
     * @return int
     */
    public function getProviderId($userId)
    {
        if($userId)
        {
            $provider = Providers::where('user_id', $userId)->first();

            if(isset($provider) && isset($provider->id))
            {
                return $provider->id;
            }
        }

        return '';
    }

    /**
     * Get Provider Companies
     * 
     * @param int $providerId
     * @return array
     */
    public function getProviderCompanies($providerId = null)
    {
        $companyIds = [];

        if($providerId)
        {
            $companyIds = CompanyProviders::where([
                'provider_id'           => $providerId,
                'accept_by_provider'    => 1,
                'accept_by_company'     => 1
            ])->pluck('company_id')->toArray();
        }

        return $companyIds;
    }

    /**
     * Get Provider Companies
     * 
     * @param int $providerId
     * @return array
     */
    public function getCompanyProviders($companyId = null)
    {
        $providerIds = [];

        if($companyId)
        {
            $providerIds = CompanyProviders::where([
                'company_id'            => $companyId,
                'accept_by_provider'    => 1,
                'accept_by_company'     => 1
            ])->pluck('company_id')->toArray();
        }

        return $providerIds;
    }

    /**
     * Add Notification
     *
     * @param array $data
     */
    public function addNotification($data = array())
    {
        if(isset($data) && count($data))
        {
            return Notifications::create($data);
        }

        return false;
    }

    /**
     * Sent Push Notification
     * 
     * @param object $user
     * @param array $payload
     * @return bool
     */
    public function sentPushNotification($user = null, $payload = null)
    {
        if($user && $payload)
        {
            if(isset($user->device_token) && strlen($user->device_token) > 4 && $user->device_type == 1)
            {
                PushNotification::iOS($payload, $user->device_token);
            }

            if(isset($user->device_token) && strlen($user->device_token) > 4 && $user->device_type == 0)
            {
                /*PushNotification::android($payload, $user->device_token);*/
            }
        }

        return true;
    }
}
