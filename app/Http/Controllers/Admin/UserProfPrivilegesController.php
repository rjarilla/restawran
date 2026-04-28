<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Repositories\EloquentUserProfPrivilegesRepository;
use Illuminate\Support\Facades\Validator;

class UserProfPrivilegesController extends Controller
{
    protected $userProfPrivilegesRepo;

    public function __construct(EloquentUserProfPrivilegesRepository $userProfPrivilegesRepo)
    {
        $this->userProfPrivilegesRepo = $userProfPrivilegesRepo;
    }

    public function index(Request $request)
    {
        $query = $request->input('search');
        $privModel = app(\App\Models\UserProfPrivileges::class);
        $userprofprivileges = $privModel->when($query, function($q) use ($query) {
                $q->where('UserProfileID', 'like', "%$query%")
                  ->orWhere('UserPrivilegesID', 'like', "%$query%") ;
            })
            ->orderByDesc('UserProfPrivilegesUpdateDate')
            ->paginate(10)
            ->appends(['search' => $query]);
        return view('admin.userprofprivileges.index', compact('userprofprivileges', 'query'));
    }

    public function create()
    {
        return view('admin.userprofprivileges.create');
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'UserProfileID' => 'required|string|max:255',
            'UserPrivilegesID' => 'required|string|max:255',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $data = $request->only(['UserProfileID', 'UserPrivilegesID']);
        $data['UserProfPrivilegesUpdateBy'] = session('user_id') ?? 'admin';
        $this->userProfPrivilegesRepo->create($data);

        return redirect()->route('admin.userprofprivileges.index')->with('success', 'User profile privilege created successfully.');
    }

    public function edit($id)
    {
        $userprofprivilege = $this->userProfPrivilegesRepo->find($id);
        if (!$userprofprivilege) {
            return redirect()->route('admin.userprofprivileges.index')->with('error', 'User profile privilege not found.');
        }
        return view('admin.userprofprivileges.edit', compact('userprofprivilege'));
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'UserProfileID' => 'required|string|max:255',
            'UserPrivilegesID' => 'required|string|max:255',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $data = $request->only(['UserProfileID', 'UserPrivilegesID']);
        $data['UserProfPrivilegesUpdateBy'] = session('user_id') ?? 'admin';
        $this->userProfPrivilegesRepo->update($id, $data);

        return redirect()->route('admin.userprofprivileges.index')->with('success', 'User profile privilege updated successfully.');
    }

    public function show($id)
    {
        $userprofprivilege = $this->userProfPrivilegesRepo->find($id);
        if (!$userprofprivilege) {
            return redirect()->route('admin.userprofprivileges.index')->with('error', 'User profile privilege not found.');
        }
        return view('admin.userprofprivileges.show', compact('userprofprivilege'));
    }

    public function destroy($id)
    {
        $this->userProfPrivilegesRepo->delete($id);
        return redirect()->route('admin.userprofprivileges.index')->with('success', 'User profile privilege deleted successfully.');
    }
}
