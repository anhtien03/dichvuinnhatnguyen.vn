<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ErpOrder extends Model
{
    use HasFactory;

    protected $table = 'erp_orders';

    protected $fillable = [
        'code',
        'customer_id',
        'customer_name',
        'customer_phone',
        'customer_address',
        'customer_company',
        'customer_tax_code',
        'production_stage',
        'status',
        'printer_name',
        'followers',
        'subtotal',
        'discount',
        'discount_mode',
        'commission_rate_snapshot',
        'commission_amount',
        'commission_status',
        'commission_period',
        'vat_rate',
        'vat_amount',
        'shipping_fee_customer',
        'shipping_fee_shop',
        'surcharge',
        'total_amount',
        'cost_amount',
        'profit_amount',
        'paid_amount',
        'payment_status',
        'is_vat_required',
        'invoice_number',
        'invoice_date',
        'invoice_status',
        'delete_requested',
        'delete_reason',
        'notes',
        'created_by',
    ];

    protected $casts = [
        'subtotal' => 'float',
        'discount' => 'float',
        'commission_rate_snapshot' => 'float',
        'commission_amount' => 'float',
        'commission_period' => 'date',
        'vat_rate' => 'float',
        'vat_amount' => 'float',
        'shipping_fee_customer' => 'float',
        'shipping_fee_shop' => 'float',
        'surcharge' => 'float',
        'total_amount' => 'float',
        'cost_amount' => 'float',
        'profit_amount' => 'float',
        'paid_amount' => 'float',
        'is_vat_required' => 'boolean',
        'delete_requested' => 'boolean',
        'invoice_date' => 'date',
    ];

    public function customer()
    {
        return $this->belongsTo(ErpCustomer::class, 'customer_id');
    }

    public function items()
    {
        return $this->hasMany(ErpOrderItem::class, 'order_id');
    }

    public function payments()
    {
        return $this->hasMany(ErpOrderPayment::class, 'order_id');
    }

    public function cashbookEntries()
    {
        return $this->hasMany(ErpCashbookEntry::class, 'order_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}

