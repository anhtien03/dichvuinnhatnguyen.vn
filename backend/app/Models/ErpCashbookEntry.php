<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ErpCashbookEntry extends Model
{
    use HasFactory;

    protected $table = 'erp_cashbook_entries';

    protected $fillable = [
        'code',
        'type',
        'category',
        'amount',
        'payment_method',
        'order_id',
        'funding_owner',
        'payer_recipient',
        'description',
        'entry_date',
        'created_by',
    ];

    protected $casts = [
        'amount' => 'float',
        'entry_date' => 'date',
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

