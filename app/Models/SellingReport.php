<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SellingReport extends Model
{
    protected $fillable = [
        'name',
        'start_date',
        'end_date',
        'excel_file_path',
        'user_id',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
