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

        if ($user) {
            // Set session
            Session::put('user_id', $user->UserID);
            Session::put('user_name', $user->UserName);

            $lookups = \App\Models\UserProfPrivileges::where('UserProfileID', $user->UserProfileID)
                    ->join('lookup', 'userprofprivileges.UserPrivilegesID', '=', 'lookup.LookupID')
                    ->select('lookup.LookupName', 'lookup.LookupValue')
                    ->get();

            // Remember me
            if ($request->has('remember')) {
                Cookie::queue('remembered_username', $user->UserName, 60 * 24 * 30);
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
