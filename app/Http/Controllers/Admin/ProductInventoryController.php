<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Repositories\EloquentProductInventoryRepository;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

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
        $productinventories = $inventoryModel->leftJoin('product', 'productinventory.ProductID', '=', 'product.ProductID')
            ->select('productinventory.*', 'product.ProductName')
            ->when($query, function($q) use ($query) {
                $q->where('productinventory.ProductBatchID', 'like', "%$query%")
                  ->orWhere('product.ProductName', 'like', "%$query%")
                  ->orWhere('productinventory.ProductQuantity', 'like', "%$query%")
                  ->orWhere('productinventory.ProductBatchDeliveryDate', 'like', "%$query%")
                  ->orWhere('productinventory.ProductBatchExpiry', 'like', "%$query%")
                  ->orWhere('productinventory.ProductReceivedBy', 'like', "%$query%") ;
            })
            ->orderByDesc('productinventory.ProductBatchDeliveryDate')
            ->paginate(10)
            ->appends(['search' => $query]);
        return view('admin.productinventory.index', compact('productinventories', 'query'));
    }

    public function create()
    {
        $products = app(\App\Models\Product::class)->all();
        return view('admin.productinventory.create', compact('products'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'ProductID' => 'required|string|max:255',
            'ProductQuantity' => 'required|numeric',
            'ProductBatchDeliveryDate' => 'required|date',
            'ProductBatchExpiry' => 'required|date',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $data = $request->only(['ProductID', 'ProductQuantity', 'ProductBatchDeliveryDate', 'ProductBatchExpiry']);
        $data['ProductReceivedBy'] = session('user_id');
        $this->productInventoryRepo->create($data);

        return redirect()->route('admin.productinventory.index')->with('success', 'Product inventory batch created successfully.');
    }

    public function edit($id)
    {
        $productinventory = $this->productInventoryRepo->find($id);
        if (!$productinventory) {
            return redirect()->route('admin.productinventory.index')->with('error', 'Product inventory batch not found.');
        }
        $products = app(\App\Models\Product::class)->all();
        return view('admin.productinventory.edit', compact('productinventory', 'products'));
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'ProductID' => 'required|string|max:255',
            'ProductQuantity' => 'required|numeric',
            'ProductBatchDeliveryDate' => 'required|date',
            'ProductBatchExpiry' => 'required|date',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $data = $request->only(['ProductID', 'ProductQuantity', 'ProductBatchDeliveryDate', 'ProductBatchExpiry']);
        $data['ProductReceivedBy'] = session('user_id');
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
