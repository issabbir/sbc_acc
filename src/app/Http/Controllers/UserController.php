<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    public function index() {
        if(!Auth::check()){
            return view('user.login');
        }else{
            return redirect()->back();
        }
    }
}
