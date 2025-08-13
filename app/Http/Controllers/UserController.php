<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class UserController extends Controller
{
    //
    public function index(){
        // This method can be used to display user-related information
        // For example, you can return a view with user data
        return view('user.index');
    }
}
