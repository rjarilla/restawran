<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Repositories\EloquentProductInventoryRepository;
use Illuminate\Support\Facades\Validator;

class ProductInventoryController extends Controller
{
    protected $productInventoryRepo;

    public function __construct(EloquentProductInventoryRepository $productInventoryRepo)
    {
        $this->productInventoryRepo = $productInventoryRepo;
    }

    public function index(Request $request)
    {
        $query = $request->input('search');
        $inventoryModel = app(\App\Models\ProductInventory::class);
        $productinventories = $inventoryModel->when($query, function($q) use ($query) {
                $q->where('ProductBatchID', 'like', "%$query%")
                  ->orWhere('ProductID', 'like', "%$query%")
                  ->orWhere('ProductQuantity', 'like', "%$query%")
                  ->orWhere('ProductBatchDeliveryDate', 'like', "%$query%")
                  ->orWhere('ProductBatchExpiry', 'like', "%$query%")
                  ->orWhere('ProductReceivedBy', 'like', "%$query%") ;
            })
            ->orderByDesc('ProductBatchDeliveryDate')
            ->paginate(10)
            ->appends(['search' => $query]);
        return view('admin.productinventory.index', compact('productinventories', 'query'));
    }

    public function create()
    {
        return view('admin.productinventory.create');
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'ProductID' => 'required|string|max:255',
            'ProductQuantity' => 'required|numeric',
            'ProductBatchDeliveryDate' => 'required|date',
            'ProductBatchExpiry' => 'required|date',
            'ProductReceivedBy' => 'required|string|max:255',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $data = $request->only(['ProductID', 'ProductQuantity', 'ProductBatchDeliveryDate', 'ProductBatchExpiry', 'ProductReceivedBy']);
        $this->productInventoryRepo->create($data);

        return redirect()->route('admin.productinventory.index')->with('success', 'Product inventory batch created successfully.');
    }

    public function edit($id)
    {
        $productinventory = $this->productInventoryRepo->find($id);
        if (!$productinventory) {
            return redirect()->route('admin.productinventory.index')->with('error', 'Product inventory batch not found.');
        }
        return view('admin.productinventory.edit', compact('productinventory'));
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'ProductID' => 'required|string|max:255',
            'ProductQuantity' => 'required|numeric',
            'ProductBatchDeliveryDate' => 'required|date',
            'ProductBatchExpiry' => 'required|date',
            'ProductReceivedBy' => 'required|string|max:255',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $data = $request->only(['ProductID', 'ProductQuantity', 'ProductBatchDeliveryDate', 'ProductBatchExpiry', 'ProductReceivedBy']);
        $this->productInventoryRepo->update($id, $data);

        return redirect()->route('admin.productinventory.index')->with('success', 'Product inventory batch updated successfully.');
    }

    public function show($id)
    {
        $productinventory = $this->productInventoryRepo->find($id);
        if (!$productinventory) {
            return redirect()->route('admin.productinventory.index')->with('error', 'Product inventory batch not found.');
        }
        return view('admin.productinventory.show', compact('productinventory'));
    }

    public function destroy($id)
    {
        $this->productInventoryRepo->delete($id);
        return redirect()->route('admin.productinventory.index')->with('success', 'Product inventory batch deleted successfully.');
    }
}
