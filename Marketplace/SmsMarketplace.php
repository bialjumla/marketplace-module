<?php
namespace Modules\Marketplace\Marketplace;

use App\Models\User;
use Modules\Marketplace\Entities\Service;
use Modules\Marketplace\Enum\MarketplaceEnum;

class SmsMarketplace
{
    private $key  = MarketplaceEnum::SMS_SERVICE;
    private $id;
    private $price;

    public function __construct()
    {
        $service     = Service::where('key',$this->key)->first();
        $this->id    = $service->id;
        $this->price = $service->price;
    }

    public function subscripe(User $user)
    {
      $checkWalletBalane = (app('wallet')->getUserWallet($user)->withdraw($this->price));

      if(!$checkWalletBalane->getData()->success)
        {
            return response()->json([
                'success'       => false,
                'icon'          => 'error',
                'message'       => 'Your Wallet Balance is not enough',
            ]);
        }
        $user->services()->attach($this->id,['status' => MarketplaceEnum::SUBSCRIPTED , 'start_date'=> now()]);
        return response()->json([
            'success'       => true,
            'icon'          => 'success',
            'message'       => 'You Have Subscriped Successfully',
        ]);
    }

    public function unSubscripe(User $user)
    {
        $checkService = $user->services->find($this->id);
        if(!$checkService)
        {
            return response()->json([
                'success'       => false,
                'icon'          => 'error',
                'message'       => 'You Have not Subscriped In This Service',
            ]);
        }
        $user->services()->detach($this->id);
        return response()->json([
            'success'       => true,
            'icon'          => 'success',
            'message'       => 'You Have UnSubscriped Successfully',
        ]);
    }

    public function upgrade(User $user)
    {
        $checkService = $user->services->find($this->id);
        if(!$checkService)
        {
            return response()->json([
                'success'       => false,
                'icon'          => 'error',
                'message'       => 'You Have not Subscriped In This Service',
            ]);
        }
        $checkWalletBalane = (app('wallet')->getUserWallet($user)->withdraw($this->price));
        if(!$checkWalletBalane->getData()->success)
        {
            return response()->json([
                'success'       => false,
                'icon'          => 'error',
                'message'       => 'Your Wallet Balance is not enough',
            ]);
        }
        $user->services()->syncWithPivotValues($this->id,['start_date'=> now(),'status' => MarketplaceEnum::UPGRADED]);
        return response()->json([
            'success'       => true,
            'icon'          => 'success',
            'message'       => 'You Have Upgraded Successfully',
        ]);
    }

}
