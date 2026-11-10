<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ErpSupplierPayment extends Model
{
    use HasFactory;

    protected $table = 'erp_supplier_payments';

    protected $fillable = [
        'supplier_id',
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

    public function supplier()
    {
        return $this->belongsTo(ErpSupplier::class, 'supplier_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}

