<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ErpOrderItem extends Model
{
    use HasFactory;

    protected $table = 'erp_order_items';

    protected $fillable = [
        'order_id',
        'product_name',
        'quantity',
        'unit',
        'unit_price',
        'subtotal',
        'discount_type',
        'discount_value',
        'supplier_id',
        'supplier_name',
        'cost_price',
        'cost_total',
        'vat_input_rate',
        'vat_input_amount',
        'vat_input_deductible',
        'specifications',
    ];

    protected $casts = [
        'quantity' => 'float',
        'unit_price' => 'float',
        'subtotal' => 'float',
        'discount_value' => 'float',
        'cost_price' => 'float',
        'cost_total' => 'float',
        'vat_input_rate' => 'float',
        'vat_input_amount' => 'float',
        'vat_input_deductible' => 'boolean',
    ];

    public function order()
    {
        return $this->belongsTo(ErpOrder::class, 'order_id');
    }

    public function supplier()
    {
        return $this->belongsTo(ErpSupplier::class, 'supplier_id');
    }
}

