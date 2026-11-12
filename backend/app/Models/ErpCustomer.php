<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ErpCustomer extends Model
{
    use HasFactory;

    protected $table = 'erp_customers';

    protected $fillable = [
        'name',
        'phone',
        'email',
        'address',
        'company_name',
        'tax_code',
        'tag',
        'commission_rate',
        'commission_type',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'commission_rate' => 'float',
        ];
    }

    public function orders()
    {
        return $this->hasMany(ErpOrder::class, 'customer_id');
    }
}

