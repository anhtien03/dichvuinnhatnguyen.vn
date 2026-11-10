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
        'notes',
    ];

    public function orders()
    {
        return $this->hasMany(ErpOrder::class, 'customer_id');
    }
}

