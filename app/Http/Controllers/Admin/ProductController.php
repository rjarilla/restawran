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
        $category_filter = $request->input('category_filter');
        $quantity_filter = $request->input('quantity_filter');
        $status_filter = $request->input('status_filter');
        $sort_by = $request->input('sort_by', 'ProductUpdatedDate');
        $sort_order = $request->input('sort_order', 'desc');

        $productModel = app(\App\Models\Product::class);
        $products = $productModel->leftJoin('lookup as category_lookup', 'product.ProductCategoryID', '=', 'category_lookup.LookupID')
            ->leftJoin('lookup as quantity_lookup', 'product.ProductQuantityTypeID', '=', 'quantity_lookup.LookupID')
            ->leftJoin('lookup as status_lookup', 'product.ProductStatus', '=', 'status_lookup.LookupID')
            ->select('product.*', 'category_lookup.LookupValue as category_value', 'quantity_lookup.LookupValue as quantity_value', 'status_lookup.LookupValue as status_value')
            ->when($query, function($q) use ($query) {
                $q->where('product.ProductName', 'like', "%$query%")
                  ->orWhere('product.ProductDescription', 'like', "%$query%")
                  ->orWhere('product.ProductID', 'like', "%$query%");
            })
            ->when($category_filter, function($q) use ($category_filter) {
                $q->where('category_lookup.LookupValue', 'like', "%$category_filter%");
            })
            ->when($quantity_filter, function($q) use ($quantity_filter) {
                $q->where('quantity_lookup.LookupValue', 'like', "%$quantity_filter%");
            })
            ->when($status_filter, function($q) use ($status_filter) {
                $q->where('status_lookup.LookupValue', 'like', "%$status_filter%");
            })
            ->orderBy($sort_by, $sort_order)
            ->paginate(10)
            ->appends(request()->query());

        return view('admin.product.index', compact('products', 'query', 'category_filter', 'quantity_filter', 'status_filter', 'sort_by', 'sort_order'));
    }

    public function create()
    {
        $allLookups = $this->lookupRepo->all();
        $categories = $allLookups->filter(function($item) { return $item->LookupCategory === 'PROD' && $item->LookupName === 'CATEGORY'; })->values();
        $quantityTypes = $allLookups->filter(function($item) { return $item->LookupCategory === 'PROD' && $item->LookupName === 'QUANTITY'; })->values();
        $statuses = $allLookups->filter(function($item) { return $item->LookupCategory === 'PROD' && $item->LookupName === 'STATUS'; })->values();
        return view('admin.product.create', compact('categories', 'quantityTypes', 'statuses'));
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

           /**   // Save normal (200x200)
            $img = Image::read($image->getRealPath())->fit(200, 200, function ($constraint) {
                $constraint->upsize();
            });
            $img->save(public_path($normalPath));

            // Save thumbnail (80x80)
            $imgThumb = Image::read($image->getRealPath())->fit(80, 80, function ($constraint) {
                $constraint->upsize();
            });
            $imgThumb->save(public_path($thumbPath));**/

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
