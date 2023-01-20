<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Todo extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id',
        'title',
        'description',
        'status',
    ];
    public function getStatusAttribute($status)
    {
        $status = 'Pending';
        if ($status == 1){
            $status = 'Completed';
        }
        return $status;
    }
}
