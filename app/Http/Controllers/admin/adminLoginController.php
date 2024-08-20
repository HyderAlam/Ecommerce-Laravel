<?php

namespace App\Http\Controllers\admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class adminLoginController extends Controller
{
    public function index()
    {
        return view('admin.login');
    }

    public function authenticate(Request $req)
    {
        $validate = Validator::make($req->all(),[
                'email' => 'required|email',
                'password' => 'required',
        ]);

        if ($validate->passes()) {  

            if(Auth::guard('admin')->attempt(['email' => $req->email, 'password' => $req->password],$req->get('remember'))) {
                $admin = Auth::guard('admin')->user();
                if($admin->role == 2){
                  return redirect()->route('admin.dashboard');                
                } else {
                    //Session Destroy with Logout 
                    Auth::guard('admin')->logout();
                  return redirect()->route('admin.login')->with('error','You are Not Authorizer to access this Page');
                }
            } else {
                return redirect()->route('admin.login')->with('error' , 'In-Valid Information  ,please try again');                
               
            }

        }else{

            return redirect()->route('admin.login')
                ->withErrors($validate)
                ->withInput($req->only('email'));

        }

        
    }

  
}
