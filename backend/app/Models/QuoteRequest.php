<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QuoteRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'customer_name',
        'phone',
        'email',
        'company',
        'product_id',
        'product_name',
        'quantity',
        'specs_description',
        'file_path',
        'file_name',
        'file_size',
        'file_type',
        'status',
        'quoted_price',
        'admin_notes',
    ];

    protected function casts(): array
    {
        return [
            'file_size' => 'integer',
            'quoted_price' => 'integer',
        ];
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}

