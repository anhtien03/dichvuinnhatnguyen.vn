<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ErpSupplier extends Model
{
    use HasFactory;

    protected $table = 'erp_suppliers';

    protected $fillable = [
        'name',
        'phone',
        'email',
        'address',
        'services',
        'bank_name',
        'bank_account',
        'bank_owner',
        'notes',
    ];

    public function orderItems()
    {
        return $this->hasMany(ErpOrderItem::class, 'supplier_id');
    }

    public function payments()
    {
        return $this->hasMany(ErpSupplierPayment::class, 'supplier_id');
    }
}

