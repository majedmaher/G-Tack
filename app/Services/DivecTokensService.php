<?php

namespace App\Services;

use App\Events\OrderCreated;
use App\Models\Address;
use App\Models\DevicesToken;
use App\Models\Order;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Throwable;

class DivecTokensService
{
    public function handle($data)
    {
        DB::beginTransaction();
        try {
            $oldFcm = DevicesToken::where('fcm_token', $data['fcm_token'])->first();
            if (!$oldFcm) {
                DevicesToken::create([
                    'fcm_token' => $data['fcm_token'],
                    'user_id' => $data['user_id'],
                    'device_name' => $data['device_name'],
                ]);
            } else {
                $oldFcm->update([
                    'fcm_token' => $data['fcm_token'],
                    'user_id' => $data['user_id'],
                    'device_name' => $data['device_name'],
                ]);
            }
            DB::commit();
        } catch (Throwable $e) {
            DB::rollBack();
            throw $e;
        }
    }
}
