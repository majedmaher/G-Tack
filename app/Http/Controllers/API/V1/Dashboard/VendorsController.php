<?php

namespace App\Http\Controllers\API\V1\Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Controllers\ControllersService;
use App\Http\Requests\VendorRequest;
use App\Http\Resources\VendorCollection;
use App\Models\Attachment;
use App\Models\Document;
use App\Models\User;
use App\Models\Vendor;
use App\Models\VendorRegions;
use App\Notifications\ResendDocumentsNotification;
use App\Notifications\VendorAcceptanceNotification;
use App\Services\CreatedLog;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Throwable;

class VendorsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $show = $request->show;
        $countRow = $request->countRow;
        $vendors = Vendor::when($show == 'new', function ($q) use ($show) {
            $q->whereHas('user', function ($qu) use ($show) {
                $qu->where('status', 'WAITING');
            });
        })
            ->when($show == 'old', function ($q) use ($show) {
                $q->whereHas('user', function ($qu) use ($show) {
                    $qu->where('status', 'ACTIVE');
                });
            })
            ->when($request->governorate, function ($q) use ($request) {
                $q->where('governorate_id', $request->governorate);
            })
            ->when($request->region, function ($q) use ($request) {
                $region = $request->region;
                $q->whereHas('regions', function ($q) use ($region) {
                    $q->where('region_id', $region);
                });
            })
            ->when($request->region_ids, function ($q) use ($request) {
                $region_ids = $request->region_ids;
                $q->whereHas('regions', function ($q) use ($region_ids) {
                    $q->whereIn('region_id', $region_ids);
                });
            })
            ->when($request->type, function ($q) use ($request) {
                $q->where('type', $request->type);
            })
            ->when($request->postingTime, function ($builder) use ($request) {
                $value = $request->postingTime;
                $weekAgo = Carbon::now()->startOfWeek()->format('Y-m-d H:i:s');
                $monthAgo = Carbon::now()->startOfMonth()->format('Y-m-d H:i:s');
                $yearAgo = Carbon::now()->startOfYear()->format('Y-m-d H:i:s');
                $last24Hours = Carbon::now()->startOfDay()->format('Y-m-d H:i:s');
                if ($value == '24') {
                    $builder->whereBetween('created_at', [$last24Hours, Carbon::now()->format('Y-m-d H:i:s')]);
                } elseif ($value == 'week') {
                    $builder->whereBetween('created_at', [$weekAgo, Carbon::now()->format('Y-m-d H:i:s')]);
                } elseif ($value == 'month') {
                    $builder->whereBetween('created_at', [$monthAgo, Carbon::now()->format('Y-m-d H:i:s')]);
                } elseif ($value == 'year') {
                    $builder->whereBetween('created_at', [$yearAgo, Carbon::now()->format('Y-m-d H:i:s')]);
                }
            })
            ->when($request->from, function ($builder) use ($request) {
                $builder->whereDate('created_at', '>=', $request->from);
            })
            ->when($request->to, function ($builder) use ($request) {
                $builder->whereDate('created_at', '<=', $request->to);
            })
            ->when($request->orderBy, function ($q) use ($request) {
                $q->orderBy('orders_count', $request->orderBy);
            })
            ->with('governorate', 'regions', 'user', 'attachments.document')
            ->withCount('reviews')
            ->withSum('reviews', 'rate')
            ->withSum('orders', 'time')
            ->withCount('orders')
            ->withAvg('orders', 'time')
            ->latest()->paginate($countRow ?? 15);

        return response()->json([
            'message' => 'تمت العمليه بنجاح',
            'code' => 200,
            'status' => true,
            'count' => $vendors->total(),
            'data' => new VendorCollection($vendors),
            'pages' => [
                'current_page' => $vendors->currentPage(),
                'total' => $vendors->total(),
                'page_size' => $vendors->perPage(),
                'next_page' => $vendors->nextPageUrl(),
                'last_page' => $vendors->lastPage(),
            ]
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(VendorRequest $request)
    {
        DB::beginTransaction();
        try {
            $user = new User();
            $user->name = $request->name;
            $user->email = $request->phone;
            $user->phone = $request->phone;
            $user->password = $request->phone;
            $user->otp = mt_rand(1000, 9999);
            $user->type = $request->type;
            $user->save();
            $vendor = new Vendor();
            $vendor->type = $request->vendor_type;
            $vendor->name = $request->name;
            $vendor->commercial_name = $request->commercial_name;
            $vendor->phone = $request->phone;
            $vendor->user_id  = $user->id;
            $vendor->max_product  = $request->max_product ?? NULL;
            $vendor->governorate_id = $request->governorate_id;
            if ($request->avatar) {
                $base64Data = $request->avatar;
                $decodedData = base64_decode($base64Data);
                $fileName = time() . '_' . Str::random(10) . '.jpg';
                $directory = 'vendor/avatars';
                $filePath = $directory . DIRECTORY_SEPARATOR . $fileName;
                File::put($filePath, $decodedData);
                $vendor->avatar = $directory . '/' . $fileName;
            }
            $vendor->save();

            foreach ($request->region_ids as $value) {
                VendorRegions::create([
                    'vendor_id' => $vendor->id,
                    'region_id' => $value,
                ]);
            }

            foreach ($request['data'] as $value) {
                $document = Document::where('id', $value['document_id'])->first();
                if ($value[$document->slug]) {
                    $base64Data = $value[$document->slug];
                    $decodedData = base64_decode($base64Data);
                    $fileName = time() . '_' . Str::random(10) . '.jpg';
                    $directory = ($value['file'] == 'IMAGE') ? 'image/vendors' : 'file/vendors';
                    $filePath = $directory . DIRECTORY_SEPARATOR . $fileName;
                    File::put($filePath, $decodedData);
                    $data['file_path'] = $directory . '/' . $fileName;
                }
                $data['document_id'] = $value['document_id'];
                $data['status'] = 'PENDING';
                $data['file_name'] = $document->name;
                $data['vendor_id'] = $vendor->id;
                if ($value['attachment_id']) {
                    Attachment::find($value['attachment_id'])->update($data);
                } else {
                    Attachment::create($data);
                }
            }

            $vendor = Vendor::with('governorate', 'regions.region', 'user', 'attachments.document')
                ->where('user_id', $user->id)
                ->withCount('reviews')
                ->withSum('reviews', 'rate')
                ->withSum('orders', 'time')
                ->withCount('orders')
                ->withAvg('orders', 'time')
                ->first();
            CreatedLog::handle('أضافة موزع جديد');

            DB::commit();
            return parent::success($vendor, "تم العملية بنجاح");
        } catch (Throwable $e) {
            DB::rollBack();
            return response([
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $vendor = Vendor::with('governorate', 'regions.region', 'user', 'attachments.document')->find($id);
        return parent::success($vendor, 'تمت العملية بنجاح');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(VendorRequest $request, $id)
    {
        DB::beginTransaction();
        try {
            $vendor = Vendor::find($id);
            $vendor->type = $request->vendor_type;
            $vendor->name = $request->name;
            $vendor->commercial_name = $request->commercial_name;
            $vendor->phone = $request->phone;
            $vendor->governorate_id = $request->governorate_id;
            if ($request->avatar) {
                $base64Data = $request->avatar;
                $decodedData = base64_decode($base64Data);
                $fileName = time() . '_' . Str::random(10) . '.jpg';
                $directory = 'vendor/avatars';
                $filePath = $directory . DIRECTORY_SEPARATOR . $fileName;
                File::put($filePath, $decodedData);
                $vendor->avatar = $directory . '/' . $fileName;
            }
            $vendor->save();
            $user = User::find($vendor->user_id);
            $user->name = $request->name;
            $user->email = $request->phone;
            $user->phone = $request->phone;
            $user->save();

            $OldVendorRegions = VendorRegions::where('vendor_id', $vendor->id)->whereNotIn('region_id', $request->region_ids)->delete();
            foreach ($request->region_ids as $value) {
                $vendorRegions = VendorRegions::where('vendor_id', $vendor->id)->where('region_id', $value)->first();
                if (!$vendorRegions) {
                    VendorRegions::create([
                        'vendor_id' => $vendor->id,
                        'region_id' => $value,
                    ]);
                }
            }

            foreach ($request['data'] as $value) {
                $document = Document::where('id', $value['document_id'])->first();
                if ($value[$document->slug]) {
                    $base64Data = $value[$document->slug];
                    $decodedData = base64_decode($base64Data);
                    $fileName = time() . '_' . Str::random(10) . '.jpg';
                    $directory = ($value['file'] == 'IMAGE') ? 'image/vendors' : 'file/vendors';
                    $filePath = $directory . DIRECTORY_SEPARATOR . $fileName;
                    File::put($filePath, $decodedData);
                    $data['file_path'] = $directory . '/' . $fileName;
                }
                $data['document_id'] = $value['document_id'];
                $data['status'] = 'PENDING';
                $data['file_name'] = $document->name;
                $data['vendor_id'] = $vendor->id;
                if ($value['attachment_id']) {
                    Attachment::find($value['attachment_id'])->update($data);
                } else {
                    Attachment::create($data);
                }
            }

            $vendor = Vendor::with('governorate', 'regions', 'user', 'attachments.document')
                ->where('user_id', $user->id)
                ->withCount('reviews')
                ->withSum('reviews', 'rate')
                ->withSum('orders', 'time')
                ->withCount('orders')
                ->withAvg('orders', 'time')
                ->first();
            CreatedLog::handle('أضافة موزع جديد');
            DB::commit();
            return parent::success($vendor, "تم العملية بنجاح");
        } catch (Throwable $e) {
            DB::rollBack();
            return response([
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        Vendor::find($id)->delete();
        CreatedLog::handle('حذف موزع');
        return ControllersService::generateProcessResponse(true, 'DELETE_SUCCESS', 200);
    }

    public function status(Request $request, $id)
    {
        $validator = Validator($request->all(), [
            'status' => 'required|in:ACTIVE,INACTIVE,WAITING,BLOCK',
        ], [
            'status.required' => 'يرجى أرسال الحالة',
            'status.in' => 'يرجى أختبار حالة بشكل صيحيح',
        ]);
        if (!$validator->fails()) {
            $vendor = User::with('vendor')->find(Vendor::find($id)->user_id);
            $vendor->update(['status' => $request->status]);
            if($request->status == "ACTIVE"){
                $vendor->notify(new VendorAcceptanceNotification());
            }
            return parent::success($vendor, "تم العملية بنجاح");
        }
        return ControllersService::generateValidationErrorMessage($validator->getMessageBag()->first(),  400);
    }

    public function active(Request $request, $id)
    {
        $validator = Validator($request->all(), [
            'active' => 'required|in:ACTIVE,INACTIVE',
        ], [
            'active.required' => 'يرجى أرسال الحالة',
            'active.in' => 'يرجى أختبار حالة بشكل صيحيح',
        ]);
        if (!$validator->fails()) {
            $vendor = Vendor::with('user')->find($id);
            $vendor->update(['active' => $request->active]);
            return parent::success($vendor, "تم العملية بنجاح");
        }
        return ControllersService::generateValidationErrorMessage($validator->getMessageBag()->first(),  400);
    }

    public function ResendDocuments($id)
    {
        $vendor = Vendor::with('user')->find($id);
        $vendor->user->notify(new ResendDocumentsNotification());
        return ControllersService::generateProcessResponse(true, 'CREATE_SUCCESS', 200);
    }

    public function statusAttachment(Request $request, $id)
    {
        $validator = Validator($request->all(), [
            'status' => 'required|in:PENDING,REJECTED,APPROVED',
        ], [
            'status.required' => 'يرجى أرسال الحالة',
            'status.in' => 'يرجى أختبار حالة بشكل صيحيح',
        ]);

        if ($validator->fails()) {
            return ControllersService::generateValidationErrorMessage($validator->getMessageBag()->first(),  400);
        }
        $attachment = Attachment::find($id);
        $attachment->update(['status' => $request->status]);
        $vendor = Vendor::with('governorate', 'region', 'user', 'attachments.document')
        ->where('id', $attachment->vendor_id)
        ->withCount('reviews')
        ->withSum('reviews', 'rate')
        ->withSum('orders', 'time')
        ->withCount('orders')
        ->withAvg('orders', 'time')
        ->first();
        return parent::success($vendor, "تم العملية بنجاح");
    }
}
