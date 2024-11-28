<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use App\Models\Recipient;

class Order extends Model
{
    use HasUuids;

    protected $fillable = [
        'description',
        'recipient_id',
        'tracking_code'
    ];

    public function recipient() {
        return $this->belongsTo(Recipient::class);
    }
}
