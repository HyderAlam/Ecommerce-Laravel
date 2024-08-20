<?php

namespace App\Http\Controllers\admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
         public function index()
         {
            return view('admin.dashboard');
         }

         public function logout()
         {
            Auth::guard('admin')->logout();
            return redirect()->route('admin.login');
         }

}
