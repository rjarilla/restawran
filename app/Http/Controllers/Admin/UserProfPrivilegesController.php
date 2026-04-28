<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Repositories\EloquentUserProfPrivilegesRepository;
use Illuminate\Support\Facades\Validator;
use App\Models\UserProfile;
use App\Models\Lookup;


class UserProfPrivilegesController extends Controller
{
    protected $userProfPrivilegesRepo;

    public function __construct(EloquentUserProfPrivilegesRepository $userProfPrivilegesRepo)
    {
        $this->userProfPrivilegesRepo = $userProfPrivilegesRepo;
    }

    public function index(Request $request)
    {
        $searchProfile   = $request->input('search_profile');
        $searchPrivilege = $request->input('search_privilege');

        $userProfiles = UserProfile::orderBy('UserProfileID')->get();
        $privileges   = Lookup::where('LookupCategory', 'PRIV') // adjust LookupCategory value as needed
                            ->orderBy('LookupName')
                            ->get();

        $userprofprivileges = \App\Models\UserProfPrivileges::with(['updatedByUser', 'userProfile'])
            ->when($searchProfile,   fn($q) => $q->where('UserProfileID',   $searchProfile))
            ->when($searchPrivilege, fn($q) => $q->where('UserPrivilegesID', $searchPrivilege))
            ->orderByDesc('UserProfPrivilegesUpdateDate')
            ->paginate(10)
            ->appends($request->only(['search_profile', 'search_privilege']));
        // dd($userprofprivileges);
        return view('admin.userprofprivileges.index', compact('userprofprivileges', 'userProfiles', 'privileges'));
    }

    public function create()
    {
        $userProfiles = UserProfile::orderBy('UserProfileID')->get();
        $privileges   = Lookup::where('LookupCategory', 'PRIV')
                            ->orderBy('LookupName')
                            ->get();

        return view('admin.userprofprivileges.create', compact('userProfiles', 'privileges'));
    }


    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'UserProfileID'    => 'required|string|max:255',
            'UserPrivilegesID' => 'required|string|max:255',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        \DB::table('userprofprivileges')->insert([
            'UserProfileID'                => $request->input('UserProfileID'),
            'UserPrivilegesID'             => $request->input('UserPrivilegesID'),
            'UserProfPrivilegesUpdateBy'   => session('user_id'),
            'UserProfPrivilegesUpdateDate' => now()->toDateString(),
        ]);

        return redirect()->route('admin.userprofprivileges.index')
            ->with('success', 'User profile privilege created successfully.');
    }

    public function edit($profile, $privilege)
    {
        $userprofprivilege = \App\Models\UserProfPrivileges::where('UserProfileID', $profile)
            ->where('UserPrivilegesID', $privilege)
            ->firstOrFail();

        $userProfiles = UserProfile::orderBy('UserProfileID')->get();
        $privileges   = Lookup::where('LookupCategory', 'PRIV')
                            ->orderBy('LookupName')
                            ->get();

        return view('admin.userprofprivileges.edit', compact('userprofprivilege', 'userProfiles', 'privileges'));
    }

    public function update(Request $request, $profile, $privilege)
    {
        $validator = Validator::make($request->all(), [
            'UserProfileID'    => 'required|string|max:255',
            'UserPrivilegesID' => 'required|string|max:255',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        // Delete old composite key row and re-insert
        // (since both columns are the PK, updating either means replacing the row)
        \DB::table('userprofprivileges')
            ->where('UserProfileID', $profile)
            ->where('UserPrivilegesID', $privilege)
            ->update([
                'UserProfileID'                => $request->input('UserProfileID'),
                'UserPrivilegesID'             => $request->input('UserPrivilegesID'),
                'UserProfPrivilegesUpdateBy'   => session('user_id'),
                'UserProfPrivilegesUpdateDate' => now()->toDateString(),
            ]);

        return redirect()->route('admin.userprofprivileges.index')
            ->with('success', 'User profile privilege updated successfully.');
    }


    public function show($id)
    {
        $userprofprivilege = $this->userProfPrivilegesRepo->find($id);
        if (!$userprofprivilege) {
            return redirect()->route('admin.userprofprivileges.index')->with('error', 'User profile privilege not found.');
        }
        return view('admin.userprofprivileges.show', compact('userprofprivilege'));
    }

    public function destroy($profile, $privilege)
    {
        \DB::table('userprofprivileges')
            ->where('UserProfileID', $profile)
            ->where('UserPrivilegesID', $privilege)
            ->delete();

        return redirect()->route('admin.userprofprivileges.index')
            ->with('success', 'Deleted successfully.');
    }
}
