<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Auth;

class Order extends Model
{
    use HasFactory, Notifiable, SoftDeletes;

    const STATUS_PENDING = 'PENDING';
    const STATUS_ACCEPTED = 'ACCEPTED';
    const STATUS_DECLINED = 'DECLINED';
    const STATUS_ONWAY = 'ONWAY';
    const STATUS_PROCESSING = 'PROCESSING';
    const STATUS_FILLED = 'FILLED';
    const STATUS_DELIVERED = 'DELIVERED';
    const STATUS_COMPLETED = 'COMPLETED';
    const STATUS_CANCELLED_BY_VENDOR = 'CANCELLED_BY_VENDOR';
    const STATUS_CANCELLED_BY_CUSTOMER = 'CANCELLED_BY_CUSTOMER';


    protected $guarded = [];

    protected static function booted()
    {
        static::creating(function (Order $order) {
            $lastNumber = Order::whereDate('created_at', now())->max('number');
            if (!$lastNumber) {
                $order->number = now()->year . '0001';
            } else {
                $order->number = $lastNumber + 1;
            }
        });

        static::created(function (Order $order) {
            OrderStatus::create([
                'order_id' => $order->id,
                'customer_id' => Auth::user()->customer->id,
                'vendor_id' => $order->vendor_id,
                'status' => 'PENDING',
                'note' => "1",
            ]);
        });
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class, 'order_id', 'id')
            ->select('id', 'order_id', 'product_id', 'product_name', 'quantity', 'custom', 'price')
            ->with(['products:id,name,size,price,image']);
    }

    public function statuses()
    {
        return $this->hasMany(OrderStatus::class, 'order_id', 'id')->with('reason');
    }

    public function vendor()
    {
        return $this->belongsTo(Vendor::class, 'vendor_id', 'id')
            ->select(
                'id',
                'type',
                'name',
                'user_id',
                'commercial_name',
                'governorate_id',
                'phone',
                'active'
            );
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id', 'id')
            ->select('id', 'name', 'user_id', 'phone');
    }

    public function address()
    {
        return $this->hasOne(OrderAddress::class, 'order_id', 'id');
    }

    public function reviews()
    {
        return $this->hasMany(Review::class, 'order_id', 'id');
    }

    public function custmer_reviews()
    {
        return $this->hasMany(Review::class, 'order_id', 'id')->where('type', 'CUSTOMER');
    }

    public function vendor_reviews()
    {
        return $this->hasMany(Review::class, 'order_id', 'id')->where('type', 'VENDOR');
    }

    public function locations()
    {
        return $this->belongsTo(Location::class, 'location_id', 'id');
    }

    public function scopeFilter(Builder $builder, $filters)
    {
        $filters = array_merge([
            'status' => null,
            'customer_id' => null,
            'type' => null,
            'map' => null,
            'status2' => null,
            'vendor_id' => null,
            'postingTime' => null,
            'from' => null,
            'to' => null,
        ], $filters);

        $builder->when($filters['status'], function ($builder, $value) {
            $builder->where('status', '=', $value);
        });
        $builder->when($filters['status2'], function ($builder, $value) {
            $builder->where('status', '=', $value);
        });

        $builder->when($filters['type'], function ($builder, $value) {
            $builder->where('type', '=', $value);
        });

        $builder->when($filters['customer_id'], function ($builder, $value) {
            $builder->where('customer_id', '=', $value);
        });

        $builder->when($filters['vendor_id'], function ($builder, $value) {
            $builder->where('vendor_id', '=', $value);
        });

        $builder->when($filters['map'], function ($builder, $value) {
            $builder->where('status', '!=', 'PENDING');
        });

        $builder->when($filters['from'], function ($builder, $value) {
            $builder->whereDate('created_at', '>=', $value);
        });

        $builder->when($filters['to'], function ($builder, $value) {
            $builder->whereDate('created_at', '<=', $value);
        });

        $builder->when($filters['postingTime'], function ($builder, $value) {
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
        });
    }

    public function scopeFilterReport(Builder $builder, $filters)
    {
        $filters = array_merge([
            'postingTime' => null,
            'from' => null,
            'to' => null,
        ], $filters);

        $builder->when($filters['from'], function ($builder, $value) {
            $builder->whereDate('orders.created_at', '>=', $value);
        });

        $builder->when($filters['to'], function ($builder, $value) {
            $builder->whereDate('orders.created_at', '<=', $value);
        });

        $builder->when($filters['postingTime'], function ($builder, $value) {
            $weekAgo = Carbon::now()->startOfWeek()->format('Y-m-d H:i:s');
            $monthAgo = Carbon::now()->startOfMonth()->format('Y-m-d H:i:s');
            $yearAgo = Carbon::now()->startOfYear()->format('Y-m-d H:i:s');
            $last24Hours = Carbon::now()->startOfDay()->format('Y-m-d H:i:s');
            if ($value == '24') {
                $builder->whereBetween('orders.created_at', [$last24Hours, Carbon::now()->format('Y-m-d H:i:s')]);
            } elseif ($value == 'week') {
                $builder->whereBetween('orders.created_at', [$weekAgo, Carbon::now()->format('Y-m-d H:i:s')]);
            } elseif ($value == 'month') {
                $builder->whereBetween('orders.created_at', [$monthAgo, Carbon::now()->format('Y-m-d H:i:s')]);
            } elseif ($value == 'year') {
                $builder->whereBetween('orders.created_at', [$yearAgo, Carbon::now()->format('Y-m-d H:i:s')]);
            }
        });
    }

    public function updateStatus($status)
    {
        if ($this->status == $status) {
            return;
        }
        $this->status = $status;
        $this->save();
        OrderStatus::create([
            'order_id' => $this->id,
            'customer_id' => $this->customer_id,
            'vendor_id' => $this->vendor_id,
            'status' => $status,
        ]);
    }

    public function getNumberAttribute()
    {
        if ($this->type == 'GAS') {
            return 'G-' . $this->attributes['id'];
        } else {
            return 'W-' . $this->attributes['id'];
        }
    }
}
