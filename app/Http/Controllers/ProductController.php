<?php

namespace App\Http\Controllers;

use Str;
use Exception;
use Inertia\Inertia;
use App\Models\Brand;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    //===========================show product page ==========================//
    public function showProduct(Request $request)
    {
        $user_id = $request->header('id');
        $products = Product::with(['brand:id,name', 'category:id,name'])
            ->where('user_id', $user_id)
            ->orderByDesc('id')
            ->get();
        return Inertia::render('Dashboard/Product/ProductPage', ['products' => $products]);
    }

    //==========================get all products for view details=======================//
    public function getAllProducts(Request $request)
    {
        $user_id = $request->header('id');
        $products = Product::with(['brand:id,name', 'category:id,name'])
            ->where('user_id', $user_id)
            ->orderByDesc('id')
            ->paginate(9);
        $productCount = Product::where('user_id', $user_id)->count();
        return Inertia::render('Dashboard/Product/AllProductsPage', [
            'products' => $products,
            'productCount' => $productCount,
        ]);
    }

    //=========================show save product page=============================//
    public function showSaveProduct(Request $request)
    {
        $id = $request->query('id');
        $user_id = $request->header('id');
        $product = Product::with(['brand:id,name', 'category:id,name'])
            ->where('user_id', $user_id)
            ->where('id', $id)
            ->first();
        $brands = Brand::select('id', 'name')->get();
        $categories = Category::where('user_id', $user_id)->get();
        return Inertia::render('Dashboard/Product/ProductSavePage', ['product' => $product, 'brands' => $brands, 'categories' => $categories]);
    }

    //===========================product create =============================//
    public function storeProduct(Request $request)
    {
        // dd($request->all());
        $user_id = $request->header('id');

        $request->validate([
            'name' => 'required|max:255',
            'price' => 'required|numeric|min:0',
            'unit' => 'required|max:10',
            'category_id' => 'required|integer|exists:categories,id',
            'brand_id' => 'nullable|integer|exists:brands,id',
            'stock' => 'required|integer|min:0',
            'image' => 'nullable|max:2048',
            'description' => 'nullable|string',
        ]);

        try {
            $image_url = null;
            if ($request->hasFile('image')) {
                $image = $request->file('image');
                $imageNameToStore = 'pos-' . uniqid() . '.' . $image->getClientOriginalExtension();
                $image_url = $image->storeAs('products', $imageNameToStore, 'public');
            }

            // Create product
            Product::create([
                'user_id' => $user_id,
                'category_id' => $request->input('category_id'),
                'brand_id' => $request->input('brand_id'),
                'name' => $request->input('name'),
                'price' => $request->input('price'),
                'unit' => $request->input('unit'),
                'stock' => $request->input('stock'),
                'description' => $request->input('description'),
                'image' => $image_url,
            ]);

            $data = ['message' => 'Product created successfully', 'status' => true, 'code' => 200];
            return to_route('product.page')->with($data);
        } catch (Exception $e) {
            $data = ['message' => 'Error creating product', 'status' => false, 'code' => 500];
            return redirect()->back()->with($data);
        }
    }

    //==========================product update==========================//
    public function updateProduct(Request $request, $id)
    {
        // dd($request->all());
        $user_id = $request->header('id');

        $product = Product::where('id', $id)->where('user_id', $user_id)->first();

        // Validation rules
        $request->validate([
            'name' => 'required|max:255',
            'price' => 'required|numeric|min:0',
            'unit' => 'required|max:10',
            'category_id' => 'required|integer|exists:categories,id',
            'brand_id' => 'nullable|integer|exists:brands,id',
            'stock' => 'required|integer|min:0',
            'image' => 'nullable|max:2048',
            'description' => 'nullable|string',
        ]);

        try {
            if ($request->hasFile('image')) {
                if ($product->image) {
                    Storage::disk('public')->delete($product->image);
                }

                $image = $request->file('image');
                $imageNameToStore = 'pos-' . uniqid() . '.' . $image->getClientOriginalExtension();
                $image_url = $image->storeAs('products', $imageNameToStore, 'public');
                $product->image = $image_url;
            }

            $product->update([
                'name' => $request->input('name'),
                'price' => $request->input('price'),
                'unit' => $request->input('unit'),
                'category_id' => $request->input('category_id'),
                'brand_id' => $request->input('brand_id'),
                'stock' => $request->input('stock'),
                'description' => $request->input('description'),
            ]);
            $data = ['message' => 'Product updated successfully', 'status' => true, ' code' => 200];
            return to_route('product.page')->with($data);
        } catch (Exception $e) {
            $data = ['message' => 'Error updating product', 'status' => false, ' code' => 500];
            return redirect()->back()->with($data);
        }
    }

    //==========================delete product==========================//
    public function deleteProduct(Request $request, $id)
    {
        $user_id = $request->header('id');

        $product = Product::where('id', $id)->where('user_id', $user_id)->first();

        try {
            if ($product->image) {
                Storage::disk('public')->delete($product->image);
            }

            $product->delete();
            $data = ['message' => 'Product deleted successfully', 'status' => true, 'code' => 200];
            return redirect()->back()->with($data);
        } catch (Exception $e) {
            $data = ['message' => 'Error deleting product', 'status' => false, 'code' => 500];
            return redirect()->back()->with($data);
        }
    }

    //=====================low stock's product=======================//
    public function getLowStockProduct(Request $request)
    {
        $user_id = $request->header('id');
        $productsInDanger = Product::with(['brand:id,name', 'category:id,name'])
            ->where('user_id', $user_id)
            ->where('stock', '<=', 5)
            ->orderByDesc('id')
            ->paginate(9);
        $productCount = $productsInDanger->count();
        return Inertia::render('Dashboard/Product/StockInDangerPage', [
            'products' => $productsInDanger,
            'productCount' => $productCount,
        ]);
    }
}
