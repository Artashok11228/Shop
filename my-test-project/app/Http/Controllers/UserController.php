<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class UserController extends Controller
{
    public function getUsers()
    {
        if(Cache::has('users'))
        {
            $users = Cache::get('users');
        } else {
            $users = User::all();
            Cache::put('users', $users , 100);
        }

        return $users;
    }
}
