<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Recipient extends Model
{
    use HasUuids;

    protected $fillable = [
        'first_name',
        'last_name',
        'address',
        'postal_code'
    ];
}
