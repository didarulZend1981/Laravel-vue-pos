<?php

namespace App\Http\Controllers;

use Exception;
use Inertia\Inertia;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CategoryController extends Controller
{
    //====================show category page======================//
    public function showCategory(Request $request)
    {
        $user_id = $request->header('id');
        $category = Category::where('user_id', $user_id)->orderBy('id', 'desc')->get();
        return Inertia::render('Dashboard/Category/CategoryPage', ['category' => $category]);
    }

    //=====================get all categories======================//
    public function getAllCategories(Request $request)
    {
        $user_id = $request->header('id');
        $categories = Category::withCount('products')->where('user_id', $user_id)->orderBy('id', 'desc')->paginate(12);
        $categoryCount = Category::where('user_id', $user_id)->count();
        return Inertia::render('Dashboard/Category/AllCategoryPage', ['categories' => $categories, 'categoryCount' => $categoryCount]);
    }

    //===================get single category with products=================//
    public function categoryById(Request $request, $id)
    {
        $user_id = $request->header('id');

        // Get the category info without loading all products
        $category = Category::where('user_id', $user_id)->where('id', $id)->firstOrFail();

        // Get paginated products related to this category
        $products = $category
            ->products()
            ->with('brand')
            ->paginate(10);

        $productCount = $products->total();

        return Inertia::render('Dashboard/Product/ProductByCategoryPage', [
            'category' => $category,
            'products' => $products,
            'productCount' => $productCount,
        ]);
    }

    //====================show save category page==================//
    public function showSaveCategory(Request $request)
    {
        $id = $request->query('id');
        $user_id = $request->header('id');

        // Fetch category for editing (if ID is provided)
        $category = $id ? Category::where('user_id', $user_id)->where('id', $id)->first() : null;

        return Inertia::render('Dashboard/Category/CategorySavePage', [
            'category' => $category,
        ]);
    }

    //=======================category create========================//
    public function storeCategory(Request $request)
    {
        $user_id = $request->header('id');

        $request->validate([
            'name' => 'required|max:20|min:2',
            'description' => 'nullable|string',
            'image' => 'nullable|max: 2048',
        ]);

        try {
            if ($request->hasFile('image')) {
                $image = $request->file('image');
                $imageNameToStore = 'product-category-' . uniqid() . '.' . $image->getClientOriginalExtension();
                $image->storeAs('category', $imageNameToStore, 'public');
            }

            Category::create([
                'name' => $request->input('name'),
                'description' => $request->input('description') ?? null,
                'user_id' => $user_id,
                'image' => $imageNameToStore,
            ]);
            $data = ['message' => 'Category created successfully', 'status' => true, 'code' => 201];
            return to_route('category.page')->with($data);
        } catch (Exception $e) {
            $data = ['message' => 'Category creation failed', 'status' => false, 'code' => 500];
            return redirect()->back()->with($data);
        }
    }

    //=========================update category=======================//
    public function updateCategory(Request $request, $id)
    {
        $user_id = $request->header('id');

        $request->validate([
            'name' => 'required|string|max:20|min:2',
            'description' => 'nullable|string',
            'image' => 'nullable|max: 2048',
        ]);

        $category = Category::where('id', $id)->where('user_id', $user_id)->first();

        try {
            if ($request->hasFile('image')) {
                if ($category->image) {
                    Storage::disk('category')->delete($category->image);
                }
                $image = $request->file('image');
                $imageNameToStore = 'product-category-' . uniqid() . '.' . $image->getClientOriginalExtension();
                $image->storeAs('category', $imageNameToStore, 'public');
            }

            $category->update([
                'name' => $request->input('name') ?? $category->name,
                'description' => $request->input('description') ?? $category->description,
                'image' => $imageNameToStore,
            ]);

            $data = ['message' => 'Category updated successfully', 'status' => true, 'code' => 200];
            return to_route('category.page')->with($data);
        } catch (Exception $e) {
            $data = ['message' => 'Category update failed', 'status' => false, 'code' => 500];
            return redirect()->back()->with($data);
        }
    }

    //=========================delete category=======================//
    public function deleteCategory(Request $request, $id){
        $user_id = $request->header('id');
        $category = Category::where('id', $id)->where('user_id', $user_id)->delete();

        if (!$category) {
            $data = ['message' => 'Category not found', 'status' => false, 'code' => 404];
            return redirect()->back()->with($data);
        }
        $data = ['message' => 'Category deleted successfully', 'status' => true, 'code' => 200];
        return redirect()->back()->with($data);
    }
}
