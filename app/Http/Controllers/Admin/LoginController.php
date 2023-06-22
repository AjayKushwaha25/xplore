<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\{Auth, Session};
use App\Http\Controllers\Controller;

class LoginController extends Controller
{
    public function login(){
    	if(Auth::check())
    		return redirect('admin/home');
        return view('admin/login');
    }
    public function register(){
    	if(Auth::check())
    		return redirect('admin/home');
        return view('admin/register');
    }
    public function logout(){
        Session::flush();        
        Auth::logout();
        return redirect('admin/login');
    }
    public function redirectTo()
    {
        return route('admin.login');
    }
}
