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
        $sort = $request->input('sort', 'ProductBatchDeliveryDate');
        $direction = $request->input('direction', 'desc');
        $sortable = [
            'ProductBatchID' => 'productinventory.ProductBatchID',
            'ProductName' => 'product.ProductName',
            'ProductQuantity' => 'productinventory.ProductQuantity',
            'ProductBatchDeliveryDate' => 'productinventory.ProductBatchDeliveryDate',
            'ProductBatchExpiry' => 'productinventory.ProductBatchExpiry',
            'ProductReceivedBy' => 'productinventory.ProductReceivedBy',
        ];
        $sortColumn = $sortable[$sort] ?? 'productinventory.ProductBatchDeliveryDate';
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
            ->orderBy($sortColumn, $direction)
            ->paginate(10)
            ->appends(['search' => $query, 'sort' => $sort, 'direction' => $direction]);
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
            'ProductID' => 'required|string|max:255|exists:product,ProductID',
            'ProductQuantity' => 'required|integer|min:0',
            'ProductQuantityRemaining' => 'required|integer|min:0',
            'ProductBatchDeliveryDate' => 'required|date',
            'ProductBatchExpiry' => 'required|date|after_or_equal:ProductBatchDeliveryDate',
            'ProductReceivedBy' => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $data = $request->only(['ProductID', 'ProductQuantity', 'ProductQuantityRemaining', 'ProductBatchDeliveryDate', 'ProductBatchExpiry', 'ProductReceivedBy']);
        $data['ProductReceivedBy'] = $data['ProductReceivedBy'] ?? session('user_id') ?? 'admin';
        // Ensure dates are filled/formatted properly
        $data['ProductBatchDeliveryDate'] = $request->input('ProductBatchDeliveryDate') ? date('Y-m-d', strtotime($request->input('ProductBatchDeliveryDate'))) : null;
        $data['ProductBatchExpiry'] = $request->input('ProductBatchExpiry') ? date('Y-m-d', strtotime($request->input('ProductBatchExpiry'))) : null;
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
            'ProductID' => 'required|string|max:255|exists:product,ProductID',
            'ProductQuantity' => 'required|integer|min:0',
            'ProductQuantityRemaining' => 'required|integer|min:0',
            'ProductBatchDeliveryDate' => 'required|date',
            'ProductBatchExpiry' => 'required|date|after_or_equal:ProductBatchDeliveryDate',
            'ProductReceivedBy' => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $data = $request->only(['ProductID', 'ProductQuantity', 'ProductQuantityRemaining', 'ProductBatchDeliveryDate', 'ProductBatchExpiry', 'ProductReceivedBy']);
        $data['ProductReceivedBy'] = $data['ProductReceivedBy'] ?? session('user_id') ?? 'admin';
        // Ensure dates are filled/formatted properly
        $data['ProductBatchDeliveryDate'] = $request->input('ProductBatchDeliveryDate') ? date('Y-m-d', strtotime($request->input('ProductBatchDeliveryDate'))) : null;
        $data['ProductBatchExpiry'] = $request->input('ProductBatchExpiry') ? date('Y-m-d', strtotime($request->input('ProductBatchExpiry'))) : null;
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

