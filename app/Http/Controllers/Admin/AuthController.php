<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Repositories\EloquentUsersRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Cookie;

class AuthController extends Controller
{
    protected $usersRepository;

    public function __construct(EloquentUsersRepository $usersRepository)
    {
        $this->usersRepository = $usersRepository;
    }

    public function showLoginForm()
    {
        // if already logged in, redirect to dashboard
        if (session('user_id')) {
            return redirect()->route('admin.index');
        }

        return view('admin.signin');
    }

    public function login(Request $request)
    {
        // validate input (MATCH your form fields: username, password)
        $request->validate([
            'username' => 'required',
            'password' => 'required',
        ]);

        // authenticate user
        $user = $this->usersRepository->login(
            $request->username,
            $request->password
        );

        // failed login
        if (!$user) {
            return back()
                ->withInput()
                ->with('login_error', 'Invalid username or password.');
        }

        // ✅ FIX: your DB primary key is "id"
        Session::put('user_id', $user->id);
        Session::put('user_name', $user->UserName);
        Session::put('user_role', $user->Role ?? null);

        // remember me cookie
        if ($request->has('remember')) {
            Cookie::queue('remembered_username', $user->UserName, 60 * 24 * 30);
        } else {
            Cookie::queue(Cookie::forget('remembered_username'));
        }

        // redirect to dashboard
        return redirect()->route('admin.index');
    }

    public function logout(Request $request)
    {
        //Session::flush();
        // 1. Clear all data from the session
        $request->session()->forget('user_id');

        // 2. Completely destroy the session and regenerate the ID (Best practice)
        $request->session()->invalidate();

        // 3. Regenerate the CSRF token to prevent token reuse
        $request->session()->regenerateToken();
    
        return redirect('/admin/signin')->with('status', 'You have been logged out.');;
    }
}