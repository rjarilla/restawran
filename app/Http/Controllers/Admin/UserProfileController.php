<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Repositories\EloquentUserProfileRepository;
use Illuminate\Support\Facades\Validator;

class UserProfileController extends Controller
{
    protected $userProfileRepo;

    public function __construct(EloquentUserProfileRepository $userProfileRepo)
    {
        $this->userProfileRepo = $userProfileRepo;
    }

    public function index(Request $request)
    {
        $query = $request->input('search');
        $profileModel = app(\App\Models\UserProfile::class);
        $userprofiles = $profileModel->when($query, function($q) use ($query) {
                $q->where('UserProfileName', 'like', "%$query%")
                  ->orWhere('UserProfileID', 'like', "%$query%") ;
            })
            ->orderByDesc('UserProfileUpdateDate')
            ->paginate(10)
            ->appends(['search' => $query]);
        return view('admin.userprofile.index', compact('userprofiles', 'query'));
    }

    public function create()
    {
        return view('admin.userprofile.create');
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'UserProfileName' => 'required|string|max:255',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $data = $request->only(['UserProfileName']);
        $data['UserProfileUpdateBy'] = session('user_id');
        $this->userProfileRepo->create($data);

        return redirect()->route('admin.userprofile.index')->with('success', 'User profile created successfully.');
    }

    public function edit($id)
    {
        $userprofile = $this->userProfileRepo->find($id);
        if (!$userprofile) {
            return redirect()->route('admin.userprofile.index')->with('error', 'User profile not found.');
        }
        return view('admin.userprofile.edit', compact('userprofile'));
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'UserProfileName' => 'required|string|max:255',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $data = $request->only(['UserProfileName']);
        $data['UserProfileUpdateBy'] = session('user_id');
        $this->userProfileRepo->update($id, $data);

        return redirect()->route('admin.userprofile.index')->with('success', 'User profile updated successfully.');
    }

    public function show($id)
    {
        $userprofile = $this->userProfileRepo->find($id);
        if (!$userprofile) {
            return redirect()->route('admin.userprofile.index')->with('error', 'User profile not found.');
        }
        return view('admin.userprofile.show', compact('userprofile'));
    }

    public function destroy($id)
    {
        $this->userProfileRepo->delete($id);
        return redirect()->route('admin.userprofile.index')->with('success', 'User profile deleted successfully.');
    }
}
