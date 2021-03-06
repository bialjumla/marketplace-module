<?php
namespace Modules\Marketplace\Services;

use Auth;
use Modules\Marketplace\Entities\Service;

class ServicesService{

    public function getAllServices()
    {
        $services     = Service::get();
        $userServices = $this->getCurrentUserServices();
        $data         = $services->merge($userServices);

        return response()->json([
            'success'       => true,
            'data'          => $data,
        ]);
    }

    public function getCurrentUserServices()
    {
        return Auth::user()->services()->get();
    }

    public function getUsersInServices($key)
    {
        return  Service::with('users')->where('key',$key)->first() ?? null;
    }

}
