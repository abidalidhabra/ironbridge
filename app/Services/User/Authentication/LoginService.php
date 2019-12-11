<?php

namespace App\Services\User\Authentication;

use App\Exceptions\AppInMaintenanceException;
use App\Repositories\AppStatisticRepository;
use App\Repositories\User\UserRepository;
use App\Services\User\Authentication\LoginFactory;
use Exception;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;
use Tymon\JWTAuth\Facades\JWTAuth;

class LoginService
{

    private $request;
    private $serverAppInfo;
    private $credentials;

    public function __construct()
    {
        $this->checkMaintenanceMode();
        $this->userRepository = new UserRepository;
    }

    /** Setters **/
    public function setRequest($request)
    {
        $this->request = $request;
        return $this;
    }

    public function setRefferedBy($refferedBy = null)
    {
        $this->user->reffered_by = $refferedBy;
        return $this;
    }

    /** Getters **/
    public function getRequest()
    {
        return $this->request;
    }

    public function getUser()
    {
        return $this->user;
    }

    public function getToken() 
    {
        $this->token = ($this->token)? $this->token: $this->guard()->attempt($this->credentials);
        return $this->token;
    }

    public function generateAToken() 
    {
        if ($this->token = $this->guard()->attempt($this->credentials)) {
            $this->user = $this->guard()->user();
            $this->invalidateTheToken();
        }else{
            throw new Exception("Invalid credentials provided");
        }
        return $this;
    }

    /**
     *
     * Additional Functionalities
     *
     */
    public function guard($guard = 'api')
    {
        return auth()->guard($guard);
    }

    public function checkMaintenanceMode()
    {
        $this->serverAppInfo = (new AppStatisticRepository)->first(['id', 'maintenance', 'android_version', 'ios_version']);
        if ($this->serverAppInfo->maintenance) {
            throw new AppInMaintenanceException("Application currently is under maintenance mode.");
        }
        return $this;
    }

    public function getServerAppInfo()
    {
        if ($this->serverAppInfo) {
            return $this->serverAppInfo;
        }else {
            $this->checkMaintenanceMode();
            return $this->serverAppInfo;
        }
    }

    public function invalidateTheToken()
    {
        if (isset($this->user->additional['token']) && !empty($this->user->additional['token'])) {
            try {
                JWTAuth::setToken($this->user->additional['token'])->invalidate();
                return ['message'=> 'Token invalidated successfully.'];
            } catch ( TokenExpiredException $exception ) {
                return ['message'=> 'Token already expired.'];
            } catch ( TokenInvalidException $exception ) {
                return ['message'=> 'Invalid token provided.'];
            } catch ( JWTException $exception ) {
                return ['message'=> 'Token is missing.'];
            }
        }
    }

    public function register()
    {
        $data = (new LoginFactory)->init($this->request->type)->login($this->request);
        $this->credentials = $data['credentials'];
        return $this;
    }

    public function setFirebaseIds()
    {
        $this->user->firebase_ids = [ 
            'android_id' => ($this->request->firebase_id && $this->request->device_type == 'android')?$this->request->firebase_id: $this->user->firebase_ids['android_id'],
            'ios_id'     => ($this->request->firebase_id && $this->request->device_type == 'ios')?$this->request->firebase_id: $this->user->firebase_ids['ios_id']
        ];
        return $this;
    }

    public function setAdditional()
    {
        $this->user->additional = [ 
            'token' =>($this->token)?  $this->token: $this->user->additional['token'],
            // 'device_type'=> ($this->request->filled('device_type'))? $this->request->device_type: $this->user->additional['device_type'],
            // 'device_id'=> ($this->request->filled('device_id'))? $this->request->device_id: $this->user->additional['device_id']
        ];
        return $this;
    }    

    public function setDeviceInfo()
    {
        $this->user->device_info = [ 
            'id'=> ($this->request->filled('device_id'))? $this->request->device_id: $this->user->device_info['id'],
            'type'=> ($this->request->filled('device_type'))? $this->request->device_type: $this->user->device_info['type'],
            'model'=> ($this->request->filled('device_model'))? $this->request->device_model: $this->user->device_info['model'],
            'os'=> ($this->request->filled('device_os'))? $this->request->device_os: $this->user->device_info['os'],
        ];
        return $this;
    }

    public function save()
    {
        $this->user->save();
        return $this;
    }
}
