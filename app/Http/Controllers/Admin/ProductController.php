<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Repositories\EloquentProductRepository;
use App\Repositories\EloquentLookupRepository;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Intervention\Image\Laravel\Facades\Image;

class ProductController extends Controller
{
    protected $productRepo;
    protected $lookupRepo;

    public function __construct(EloquentProductRepository $productRepo, EloquentLookupRepository $lookupRepo)
    {
        $this->productRepo = $productRepo;
        $this->lookupRepo = $lookupRepo;
    }

    public function index(Request $request)
    {
        $query = $request->input('search');
        $sort_by = $request->input('sort_by', 'created_at');
        $sort_order = $request->input('sort_order', 'desc');

        $productModel = app(\App\Models\Product::class);
        $products = $productModel->when($query, function($q) use ($query) {
                $q->where('ProductName', 'like', "%$query%")
                  ->orWhere('ProductDescription', 'like', "%$query%")
                  ->orWhere('ProductID', 'like', "%$query%");
            })
            ->orderBy($sort_by, $sort_order)
            ->paginate(10)
            ->appends(request()->query());

        return view('admin.product.index', compact('products', 'query', 'sort_by', 'sort_order'));
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
            'ProductPrice' => 'required|numeric',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $data = $request->only(['ProductName', 'ProductDescription', 'ProductPrice']);

        $this->productRepo->create($data);

        return redirect()->route('admin.product.index')->with('success', 'Product created successfully');
    }

    public function edit($id)
    {
        $product = $this->productRepo->find($id);
        if (!$product) {
            return redirect()->route('admin.product.index')->with('error', 'Product not found.');
        }
        $allLookups = $this->lookupRepo->all();
        $categories = $allLookups->filter(function($item) { return $item->LookupCategory === 'PROD' && $item->LookupName === 'CATEGORY'; })->values();
        $quantityTypes = $allLookups->filter(function($item) { return $item->LookupCategory === 'PROD' && $item->LookupName === 'QUANTITY'; })->values();
        $statuses = $allLookups->filter(function($item) { return $item->LookupCategory === 'PROD' && $item->LookupName === 'STATUS'; })->values();
        return view('admin.product.edit', compact('product', 'categories', 'quantityTypes', 'statuses'));
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
            //$filename = Str::uuid() . '.' . $image->getClientOriginalExtension();
            $filename = pathinfo($image->getClientOriginalName(), PATHINFO_FILENAME) . '.' . $image->getClientOriginalExtension();
            $normalPath = '/assets/images/products/normal/' . $filename;
            $thumbPath = '/assets/images/products/thumbs/' . $filename;
            try {
            // Save normal (200x200)
       /*     $img = Image::read($image->getRealPath())->fit(200, 200, function ($constraint) {
                $constraint->upsize();
            });
            $img->save(public_path($normalPath));

            // Save thumbnail (80x80)
            $imgThumb = Image::read($image->getRealPath())->fit(80, 80, function ($constraint) {
                $constraint->upsize();
            });
            $imgThumb->save(public_path($thumbPath));*/
            $imageSize = getImageSize($image);
            $imageWidth = $imageSize[0];
            $imageHeight = $imageSize[1];

            $DESIRED_WIDTH = 200;

            $proportionalHeight = round(($DESIRED_WIDTH * $imageHeight) / $imageWidth);

            $originalImage = imageCreateFromJPEG($image);

            $resizedImage = imageCreateTrueColor($DESIRED_WIDTH, $proportionalHeight);
            imageCopyResampled($resizedImage, $originalImage, 0, 0, 0, 0, $DESIRED_WIDTH, $proportionalHeight, $imageWidth, $imageHeight);

            imageJPEG($resizedImage, public_path($normalPath), 90);
            imagedestroy($originalImage);
            imagedestroy($resizedImage);

            $DESIRED_WIDTH = 200;

            $proportionalHeight = round(($DESIRED_WIDTH * $imageHeight) / $imageWidth);

            $originalImage = imageCreateFromJPEG($image);

            $resizedImage = imageCreateTrueColor($DESIRED_WIDTH, $proportionalHeight);
            imageCopyResampled($resizedImage, $originalImage, 0, 0, 0, 0, $DESIRED_WIDTH, $proportionalHeight, $imageWidth, $imageHeight);

            imageJPEG($resizedImage, public_path($normalPath), 90);
            
            imagedestroy($resizedImage);                                                   

            $DESIRED_WIDTH = 80;
            $proportionalHeight = round(($DESIRED_WIDTH * $imageHeight) / $imageWidth);
            $resizedImage = imageCreateTrueColor($DESIRED_WIDTH, $proportionalHeight);
            imageCopyResampled($resizedImage, $originalImage, 0, 0, 0, 0, $DESIRED_WIDTH, $proportionalHeight, $imageWidth, $imageHeight);

            imageJPEG($resizedImage, public_path($thumbPath), 90);
            
            imagedestroy($resizedImage);
            imagedestroy($originalImage);

            $imagePath = $normalPath;
            } catch (\Exception $e) {
                return redirect()->back()->with('error', 'Image processing failed: ' . $e->getMessage())->withInput();
            }
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
