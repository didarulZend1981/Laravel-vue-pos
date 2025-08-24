<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use Exception;
use Illuminate\Http\Request;
use Inertia\Inertia;

class BrandController extends Controller
{
    //====================show brand page======================//
    public function showBrand(Request $request)
    {
        $user_id = $request->header('id');
        $brand = Brand::where('user_id', $user_id)->orderBy('id', 'desc')->get();
        return Inertia::render('Dashboard/Brand/BrandPage', ['brand' => $brand]);
    }

    //==========================show brand create page=================//
    public function showSaveBrand(Request $request){
        $id = $request->query('id');
        $user_id = $request->header('id');

        $brand = Brand::where('user_id', $user_id)->where('id', $id)->first();

        return Inertia::render('Dashboard/Brand/BrandSavePage', [
            'brand' => $brand, // Pass brand as key-value pair
        ]);
    }

    //=======================brand create========================//
    public function storeBrand(Request $request)
    {
        $user_id = $request->header('id');

        $request->validate([
            'name' => 'required|max:50|min:2',
            'description' => 'nullable|string',
        ]);

        try {
            Brand::create([
                'name' => $request->input('name'),
                'description' => $request->input('description') ?? null,
                'user_id' => $user_id,
            ]);

            $data = ['message' => 'Brand created successfully', 'status' => true, 'code' => 201];
            return to_route('brand.page')->with($data);
        } catch (Exception $e) {
            $data = ['message' => 'Error creating brand', 'status' => false, 'code' => 500];
            return redirect()->back()->with($data);
        }
    }

    //=========================update brand=======================//
    public function updateBrand(Request $request, $id)
    {
        $user_id = $request->header('id');

        $request->validate([
            'name' => 'required|max:50|min:2',
            'description' => 'nullable|string',
        ]);

        $brand = Brand::where('id', $id)->where('user_id', $user_id)->first();

        try {
            $brand->update([
                'name' => $request->input('name') ?? $brand->name,
                'description' => $request->input('description') ?? $brand->description,
            ]);

            $data = ['message' => 'Brand updated successfully', 'status' => true, 'code' => 201];
            return to_route('brand.page')->with($data);
        } catch (Exception $e) {
            $data = ['message' => 'Error updating brand', 'status' => false, 'code' => 500];
            return redirect()->back()->with($data);
        }
    }

        //=========================delete brand=======================//
        public function deleteBrand(Request $request, $id)
        {
            $user_id = $request->header('id');
            $brand = Brand::where('id', $id)->where('user_id', $user_id)->first();

            if (!$brand) {
                $data = ['message' => 'Brand Not found', 'status' => false, 'code' => 400];
                return redirect()->back()->with($data);
            }

            $brand->delete();

            $data = ['message' => 'Brand deleted successfully', 'status' => true, 'code' => 200];
            return redirect()->back()->with($data);
        }
}
