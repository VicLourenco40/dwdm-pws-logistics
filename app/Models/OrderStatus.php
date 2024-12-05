<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Status;

class OrderStatus extends Model
{
    protected $table = 'order_status';

    protected $fillable = [
        'order_id',
        'status_id',
        'changed_by'
    ];

    public function status() {
        return $this->belongsTo(Status::class);
    }
}
