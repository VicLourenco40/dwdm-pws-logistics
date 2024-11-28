<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;
use App\Models\User;
use App\Models\Role;

class UserController extends Controller
{
    public function register(Request $request)
    {
        $role = Role::where('title', 'Courier')->first();

        $user = new User();
        $user->name = $request->name;
        $user->email = $request->email;
        $user->nif = $request->nif;
        $user->password = bcrypt($request->password);
        $user->role()->associate($role);
        $user->save();

        $token = JWTAuth::claims([
            'role' => $user->role
        ])->fromUser($user);

        return response()->json([
            'token' => $token
        ]);
    }
}
