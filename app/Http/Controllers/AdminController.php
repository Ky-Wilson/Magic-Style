<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AdminController extends Controller
{
    //
    public function index(){
        return view('admin.index');
        // This method can be used to display admin-related information
    }
}
