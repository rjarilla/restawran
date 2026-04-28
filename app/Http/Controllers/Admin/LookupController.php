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
        $lookups = $lookupModel->leftJoin('rt_users', 'lookup.LookupUpdateBy', '=', 'rt_users.UserID')
            ->select('lookup.*', 'rt_users.UserName as UpdatedByName')
            ->when($query, function($q) use ($query) {
                $q->where('lookup.LookupCategory', 'like', "%$query%")
                  ->orWhere('lookup.LookupName', 'like', "%$query%")
                  ->orWhere('lookup.LookupValue', 'like', "%$query%")
                  ->orWhere('rt_users.UserName', 'like', "%$query%")
                  ->orWhere('lookup.LookupUpdateDate', 'like', "%$query%")
                  ->orWhere('lookup.LookupID', 'like', "%$query%");
            })
            ->when($categoryFilter, function($q) use ($categoryFilter) {
                $q->where('lookup.LookupCategory', 'like', "%$categoryFilter%");
            })
            ->when($nameFilter, function($q) use ($nameFilter) {
                $q->where('lookup.LookupName', 'like', "%$nameFilter%");
            });

        // Apply sorting
        switch ($sortBy) {
            case 'date_asc':
                $lookups = $lookups->orderBy('lookup.LookupUpdateDate', 'asc');
                break;
            case 'category_asc':
                $lookups = $lookups->orderBy('lookup.LookupCategory', 'asc');
                break;
            case 'category_desc':
                $lookups = $lookups->orderBy('lookup.LookupCategory', 'desc');
                break;
            case 'name_asc':
                $lookups = $lookups->orderBy('lookup.LookupName', 'asc');
                break;
            case 'name_desc':
                $lookups = $lookups->orderBy('lookup.LookupName', 'desc');
                break;
            case 'date_desc':
            default:
                $lookups = $lookups->orderByDesc('lookup.LookupUpdateDate');
                break;
        }

        $lookups = $lookups->paginate(10)
            ->appends(['search' => $query, 'category_filter' => $categoryFilter, 'name_filter' => $nameFilter, 'sort_by' => $sortBy]);
        
        return view('admin.lookup.index', compact('lookups', 'query', 'categoryFilter', 'nameFilter', 'sortBy'));
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
