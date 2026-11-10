<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ErpOrderPayment extends Model
{
    use HasFactory;

    protected $table = 'erp_order_payments';

    protected $fillable = [
        'order_id',
        'amount',
        'payment_method',
        'funding_owner',
        'note',
        'paid_at',
        'created_by',
    ];

    protected $casts = [
        'amount' => 'float',
        'paid_at' => 'datetime',
    ];

    public function order()
    {
        return $this->belongsTo(ErpOrder::class, 'order_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}

