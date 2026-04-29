<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Repositories\EloquentUsersRepository;
use Illuminate\Support\Facades\Validator;

class UsersController extends Controller
{
    protected $usersRepo;

    public function __construct(EloquentUsersRepository $usersRepo)
    {
        $this->usersRepo = $usersRepo;
    }

    public function index(Request $request)
    {
        $query = $request->input('search');
        $usersModel = app(\App\Models\Users::class);
        $users = $usersModel->with(['userProfile'])
            ->when($query, function($q) use ($query) {
                $q->where('UserName', 'like', "%$query%")
                  ->orWhere('UserStatus', 'like', "%$query%")
                  ->orWhere('UserID', 'like', "%$query%") ;
            })
            ->orderByDesc('UserUpdateDate')
            ->paginate(10)
            ->appends(['search' => $query]);
        return view('admin.users.index', compact('users', 'query'));
    }

    public function create()
    {
        return view('admin.users.create');
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'UserName' => 'required|string|max:255',
            'UserPassword' => 'required|string|min:6',
            'UserProfileID' => 'required|string|max:255',
            'UserStatus' => 'required|string|max:255',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $data = $request->only(['UserName', 'UserPassword', 'UserProfileID', 'UserStatus']);
        $data['UserUpdateBy'] = session('user_id') ?? 'admin';
        $this->usersRepo->create($data);

        return redirect()->route('admin.users.index')->with('success', 'User created successfully.');
    }

    public function edit($id)
    {
        $user = $this->usersRepo->find($id);
        if (!$user) {
            return redirect()->route('admin.users.index')->with('error', 'User not found.');
        }
        return view('admin.users.edit', compact('user'));
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'UserName' => 'required|string|max:255',
            'UserPassword' => 'nullable|string|min:6',
            'UserProfileID' => 'required|string|max:255',
            'UserStatus' => 'required|string|max:255',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $data = $request->only(['UserName', 'UserProfileID', 'UserStatus']);
        if ($request->filled('UserPassword')) {
            $data['UserPassword'] = $request->input('UserPassword');
        }
        $data['UserUpdateBy'] = session('user_id') ?? 'admin';
        $this->usersRepo->update($id, $data);

        return redirect()->route('admin.users.index')->with('success', 'User updated successfully.');
    }

    public function show($id)
    {
        $user = $this->usersRepo->find($id);
        if (!$user) {
            return redirect()->route('admin.users.index')->with('error', 'User not found.');
        }
        return view('admin.users.show', compact('user'));
    }

    public function destroy($id)
    {
        $this->usersRepo->delete($id);
        return redirect()->route('admin.users.index')->with('success', 'User deleted successfully.');
    }
}
