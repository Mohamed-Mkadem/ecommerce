<?php

namespace App\Models;

use App\Models\Invoice;
use Illuminate\Database\Eloquent\Model;

class Invoiceable extends Model
{
    public function invoices()
    {
        return $this->morphMany(Invoice::class, 'invoiceable');
    }
}
