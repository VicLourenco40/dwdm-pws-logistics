<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Status;

class StatusController extends Controller
{
    public function index() {
        $statuses = Status::get();

        return response()->json([
            'statuses' => $statuses
        ]);
    }
}
