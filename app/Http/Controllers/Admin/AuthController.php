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
        return view('admin/signin');
    }

    public function login(Request $request)
    {
        $request->validate([
            'username' => 'required',
            'password' => 'required',
        ]);

        $user = $this->usersRepository->login($request->input('username'), $request->input('password'));

        if ($user) {
            // Set session
            Session::put('user_id', $user->UserID);
            Session::put('user_name', $user->UserName);
            // Remember me
            if ($request->has('remember')) {
                Cookie::queue('remembered_username', $user->UserName, 60 * 24 * 30); // 30 days
            } else {
                Cookie::queue(Cookie::forget('remembered_username'));
            }
            return redirect('admin/index');
        } else {
            return redirect()->back()->withInput()->with('login_error', 'Invalid username or password.');
        }
    }

    public function logout()
    {
        Session::flush();
        return redirect('/admin');
    }
}
