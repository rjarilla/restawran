<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Repositories\EloquentProductRepository;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Intervention\Image\Facades\Image;

class ProductController extends Controller
{
    protected $productRepo;

    public function __construct(EloquentProductRepository $productRepo)
    {
        $this->productRepo = $productRepo;
    }

    public function index(Request $request)
    {
        $query = $request->input('search');
        $productModel = app(\App\Models\Product::class);
        $products = $productModel->when($query, function($q) use ($query) {
                $q->where('ProductName', 'like', "%$query%")
                  ->orWhere('ProductDescription', 'like', "%$query%")
                  ->orWhere('ProductCategoryID', 'like', "%$query%")
                  ->orWhere('ProductQuantityTypeID', 'like', "%$query%")
                  ->orWhere('ProductStatus', 'like', "%$query%")
                  ->orWhere('ProductID', 'like', "%$query%") ;
            })
            ->orderByDesc('ProductUpdatedDate')
            ->paginate(10)
            ->appends(['search' => $query]);
        return view('admin.product.index', compact('products', 'query'));
    }

    public function create()
    {
        return view('admin.product.create');
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'ProductName' => 'required|string|max:255',
            'ProductDescription' => 'nullable|string',
            'ProductCategoryID' => 'required|string|max:255',
            'ProductQuantityTypeID' => 'required|string|max:255',
            'ProductPrice' => 'required|numeric',
            'ProductStatus' => 'required|string|max:255',
            'ProductImage' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'ExistingProductImage' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $data = $request->only(['ProductName', 'ProductDescription', 'ProductCategoryID', 'ProductQuantityTypeID', 'ProductPrice', 'ProductStatus']);
        $data['ProductUpdatedBy'] = session('user_id') ?? 'admin';

        // Handle image upload or selection
        $imagePath = null;
        if ($request->hasFile('ProductImage')) {
            $image = $request->file('ProductImage');
            $filename = Str::uuid() . '.' . $image->getClientOriginalExtension();
            $normalPath = 'assets/images/products/normal/' . $filename;
            $thumbPath = 'assets/images/products/thumbs/' . $filename;

            // Save normal (200x200)
            $img = Image::make($image->getRealPath())->fit(200, 200, function ($constraint) {
                $constraint->upsize();
            });
            $img->save(public_path($normalPath));

            // Save thumbnail (80x80)
            $imgThumb = Image::make($image->getRealPath())->fit(80, 80, function ($constraint) {
                $constraint->upsize();
            });
            $imgThumb->save(public_path($thumbPath));

            $imagePath = $normalPath;
        } elseif ($request->input('ExistingProductImage')) {
            $imagePath = $request->input('ExistingProductImage');
        } else {
            $imagePath = 'assets/images/products/default.png';
        }
        $data['ProductImagePath'] = $imagePath;
        $this->productRepo->create($data);

        return redirect()->route('admin.product.index')->with('success', 'Product created successfully.');
    }

    public function edit($id)
    {
        $product = $this->productRepo->find($id);
        if (!$product) {
            return redirect()->route('admin.product.index')->with('error', 'Product not found.');
        }
        return view('admin.product.edit', compact('product'));
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'ProductName' => 'required|string|max:255',
            'ProductDescription' => 'nullable|string',
            'ProductCategoryID' => 'required|string|max:255',
            'ProductQuantityTypeID' => 'required|string|max:255',
            'ProductPrice' => 'required|numeric',
            'ProductStatus' => 'required|string|max:255',
            'ProductImage' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'ExistingProductImage' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $data = $request->only(['ProductName', 'ProductDescription', 'ProductCategoryID', 'ProductQuantityTypeID', 'ProductPrice', 'ProductStatus']);
        $data['ProductUpdatedBy'] = session('user_id') ?? 'admin';

        // Handle image upload or selection
        $imagePath = null;
        if ($request->hasFile('ProductImage')) {
            $image = $request->file('ProductImage');
            $filename = Str::uuid() . '.' . $image->getClientOriginalExtension();
            $normalPath = 'assets/images/products/normal/' . $filename;
            $thumbPath = 'assets/images/products/thumbs/' . $filename;

            // Save normal (200x200)
            $img = Image::make($image->getRealPath())->fit(200, 200, function ($constraint) {
                $constraint->upsize();
            });
            $img->save(public_path($normalPath));

            // Save thumbnail (80x80)
            $imgThumb = Image::make($image->getRealPath())->fit(80, 80, function ($constraint) {
                $constraint->upsize();
            });
            $imgThumb->save(public_path($thumbPath));

            $imagePath = $normalPath;
        } elseif ($request->input('ExistingProductImage')) {
            $imagePath = $request->input('ExistingProductImage');
        }

        if ($imagePath) {
            $data['ProductImagePath'] = $imagePath;
        }
        $this->productRepo->update($id, $data);

        return redirect()->route('admin.product.index')->with('success', 'Product updated successfully.');
    }

    public function show($id)
    {
        $product = $this->productRepo->find($id);
        if (!$product) {
            return redirect()->route('admin.product.index')->with('error', 'Product not found.');
        }
        return view('admin.product.show', compact('product'));
    }

    public function destroy($id)
    {
        $this->productRepo->delete($id);
        return redirect()->route('admin.product.index')->with('success', 'Product deleted successfully.');
    }
}
