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

    public function login(Request $request)
    {
        // MUST MATCH FORM INPUT NAMES
        $request->validate([
            'UserName' => 'required',
            'Password' => 'required',
        ]);

        $user = $this->usersRepository->login(
            $request->UserName,
            $request->Password
        );

        if (!$user) {
            return back()
                ->withInput()
                ->with('login_error', 'Invalid username or password.');
        }

        Session::put('user_id', $user->UserID);
        Session::put('user_name', $user->UserName);

        if ($request->has('remember')) {
            Cookie::queue('remembered_username', $user->UserName, 60 * 24 * 30);
        } else {
            Cookie::queue(Cookie::forget('remembered_username'));
        }

        return redirect('admin/index');
    }

    public function logout()
    {
        Session::flush();
        return redirect('/admin/signin');
    }
}