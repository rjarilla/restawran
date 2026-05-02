<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Repositories\EloquentUserProfPrivilegesRepository;
use Illuminate\Support\Facades\Validator;
use App\Models\UserProfile;
use App\Models\UserProfPrivileges;
use App\Models\Lookup;
use Illuminate\Validation\Rule;

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
        $privileges   = Lookup::where('LookupCategory', 'PRIV')
                            ->orderBy('LookupName')
                            ->get();

        $userprofprivileges = UserProfPrivileges::with(['updatedByUser', 'userProfile'])
            ->when($searchProfile,   fn($q) => $q->where('UserProfileID',   $searchProfile))
            ->when($searchPrivilege, fn($q) => $q->where('UserPrivilegesID', $searchPrivilege))
            ->orderByDesc('UserProfPrivilegesUpdateDate')
            ->paginate(10)
            ->appends($request->only(['search_profile', 'search_privilege']));

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
            'UserProfileID' => ['required', 'string', 'max:255'],
            'UserPrivilegesID' => [
                'required',
                'string',
                'max:255',
                Rule::unique('userprofprivileges')->where(fn($q) =>
                    $q->where('UserProfileID', $request->UserProfileID)
                      ->where('UserPrivilegesID', $request->UserPrivilegesID)
                ),
            ],
        ], ['UserPrivilegesID.unique' => 'This privilege is already added.']);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $this->userProfPrivilegesRepo->create([
            'UserProfileID'                => $request->input('UserProfileID'),
            'UserPrivilegesID'             => $request->input('UserPrivilegesID'),
            'UserProfPrivilegesUpdateBy'   => session('user_id'),
            'UserProfPrivilegesUpdateDate' => now(),
        ]);

        return redirect()->route('admin.userprofprivileges.index')
            ->with('success', 'User profile privilege created successfully.');
    }

    public function edit($profile, $privilege)
    {
        $userprofprivilege = $this->userProfPrivilegesRepo->findByCompositeKey($profile, $privilege);

        if (!$userprofprivilege) {
            return redirect()->route('admin.userprofprivileges.index')
                ->with('error', 'User profile privilege not found.');
        }

        $userProfiles = UserProfile::orderBy('UserProfileID')->get();
        $privileges   = Lookup::where('LookupCategory', 'PRIV')
                            ->orderBy('LookupName')
                            ->get();

        return view('admin.userprofprivileges.edit', compact('userprofprivilege', 'userProfiles', 'privileges'));
    }

    public function update(Request $request, $profile, $privilege)
    {
        $validator = Validator::make($request->all(), [
            'UserProfileID'    => ['required', 'string', 'max:255'],
            'UserPrivilegesID' => [
                'required',
                'string',
                'max:255',
                Rule::unique('userprofprivileges')->where(fn($q) =>
                    $q->where('UserProfileID', $request->UserProfileID)
                      ->where('UserPrivilegesID', $request->UserPrivilegesID)
                ),
            ],
        ], ['UserPrivilegesID.unique' => 'This privilege is already added.']);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $this->userProfPrivilegesRepo->updateByCompositeKey($profile, $privilege, [
            'UserProfileID'                => $request->input('UserProfileID'),
            'UserPrivilegesID'             => $request->input('UserPrivilegesID'),
            'UserProfPrivilegesUpdateBy'   => session('user_id'),
            'UserProfPrivilegesUpdateDate' => now()->toDateString(),
        ]);

        return redirect()->route('admin.userprofprivileges.index')
            ->with('success', 'User profile privilege updated successfully.');
    }

    public function show($profile, $privilege)
    {
        $userprofprivilege = $this->userProfPrivilegesRepo->findByCompositeKey($profile, $privilege);

        if (!$userprofprivilege) {
            return redirect()->route('admin.userprofprivileges.index')
                ->with('error', 'User profile privilege not found.');
        }

        return view('admin.userprofprivileges.show', compact('userprofprivilege'));
    }

    public function destroy($profile, $privilege)
    {
        $this->userProfPrivilegesRepo->deleteByCompositeKey($profile, $privilege);

        return redirect()->route('admin.userprofprivileges.index')
            ->with('success', 'Deleted successfully.');
    }
}
