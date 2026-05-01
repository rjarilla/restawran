<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Repositories\EloquentLookupRepository;
use Illuminate\Support\Facades\Validator;

class LookupController extends Controller
{
    protected $lookupRepo;

    public function __construct(EloquentLookupRepository $lookupRepo)
    {
        $this->lookupRepo = $lookupRepo;
    }

    public function index(Request $request)
    {
        $query = $request->input('search');
        $categoryFilter = $request->input('category_filter');
        $nameFilter = $request->input('name_filter');
        $sortBy = $request->input('sort_by', 'date_desc');
        
        $lookupModel = app(\App\Models\Lookup::class);
         $lookupModel = app(\App\Models\Lookup::class);
        $lookups = $lookupModel->when($query, function($q) use ($query) {
                $q->where('LookupCategory', 'like', "%$query%")
                  ->orWhere('LookupName', 'like', "%$query%")
                  ->orWhere('LookupValue', 'like', "%$query%")
                  ->orWhere('LookupUpdateBy', 'like', "%$query%")
                  ->orWhere('LookupUpdateDate', 'like', "%$query%")
                  ->orWhere('LookupID', 'like', "%$query%") ;
            })
            ->orderByDesc('LookupUpdateDate')
            ->paginate(10)
            ->appends(['search' => $query]);
        return view('admin.lookup.index', compact('lookups', 'query'));
    }

    public function create()
    {
        $categories = $this->lookupRepo->all()->pluck('LookupCategory')->unique()->filter()->values();
        $names = $this->lookupRepo->all()->pluck('LookupName')->unique()->filter()->values();
        return view('admin.lookup.create', compact('categories', 'names'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'LookupCategory' => 'required|string|max:255',
            'LookupName' => 'required|string|max:255',
            'LookupValue' => 'required|string|max:255',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $data = $request->only(['LookupCategory', 'LookupName', 'LookupValue']);
        $data['LookupUpdateBy'] = session('user_id') ?? 'admin';
        $this->lookupRepo->create($data);

        return redirect()->route('admin.lookup.index')->with('success', 'Lookup created successfully.');
    }

    public function edit($id)
    {
        $lookup = $this->lookupRepo->find($id);
        if (!$lookup) {
            return redirect()->route('admin.lookup.index')->with('error', 'Lookup not found.');
        }
        $categories = $this->lookupRepo->all()->pluck('LookupCategory')->unique()->filter()->values();
        $names = $this->lookupRepo->all()->pluck('LookupName')->unique()->filter()->values();
        return view('admin.lookup.edit', compact('lookup', 'categories', 'names'));
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'LookupCategory' => 'required|string|max:255',
            'LookupName' => 'required|string|max:255',
            'LookupValue' => 'required|string|max:255',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $data = $request->only(['LookupCategory', 'LookupName', 'LookupValue']);
        $data['LookupUpdateBy'] = session('user_id') ?? 'admin';
        $this->lookupRepo->update($id, $data);

        return redirect()->route('admin.lookup.index')->with('success', 'Lookup updated successfully.');
    }

    public function show($id)
    {
        $lookup = $this->lookupRepo->find($id);
        if (!$lookup) {
            return redirect()->route('admin.lookup.index')->with('error', 'Lookup not found.');
        }
        return view('admin.lookup.show', compact('lookup'));
    }

    public function destroy($id)
    {
        $this->lookupRepo->delete($id);
        return redirect()->route('admin.lookup.index')->with('success', 'Lookup deleted successfully.');
    }
}
