<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class OrderItem extends Model
{
    use HasFactory , SoftDeletes;

    protected $guarded = [];

    public function products()
    {
        return $this->belongsTo(Product::class , 'product_id' , 'id');
    }

    public function order()
    {
        return $this->belongsTo(Order::class , 'order_id' , 'id');
    }

    public function scopeFilter(Builder $builder, $filters)
    {
        $filters = array_merge([
            'postingTime' => null,
            'from' => null,
            'to' => null,
        ], $filters);

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
}
