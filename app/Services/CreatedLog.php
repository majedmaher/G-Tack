<?php

namespace App\Services;

use App\Models\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Throwable;

class CreatedLog
{
    public static function handle($content)
    {
        DB::beginTransaction();
        try {
            Log::create([
                'user_id' => Auth::user()->id,
                'content' => $content,
            ]);
            DB::commit();
        } catch (Throwable $e) {
            DB::rollBack();
            throw $e;
        }
    }
}
