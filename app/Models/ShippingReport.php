<?php

namespace App\Models;

use App\Models\Shipper;
use Illuminate\Database\Eloquent\Model;

class ShippingReport extends Model
{
    protected $fillable = [
        'orders_count',
        'excel_file_path',
        'pdf_file_path',
        'date',
        'shipper_id',
        'name'
    ];

    public function shipper()
    {
        return $this->belongsTo(Shipper::class);
    }
}
