<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
class Earn extends Model
{
    use HasFactory;


    public function scopeCreatedToday($query)
    {   
        Carbon::now()->setTimezone('UTC');
        var_dump(Carbon::today()->toDateString());
        return $query->whereDate('created_at', '>=', Carbon::today()->toDateString());
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
