<?php

namespace App\Http\Controllers;

use Exception;
use Inertia\Inertia;
use App\Models\Brand;
use App\Models\PurchaseInvoice;
use App\Models\Supplier;
use Illuminate\Http\Request;

class SupplierController extends Controller
{
    //====================show supplier page=====================//
    public function showSupplier(Request $request)
    {
        $user_id = $request->header('id');
        $supplier = Supplier::where('user_id', $user_id)->with('brand:id,name')->orderBy('id', 'desc')->get();
        return Inertia::render('Dashboard/Supplier/SupplierPage', ['supplier' => $supplier]);
    }

    //=====================show save supplier page====================//
    public function showSaveSupplier(Request $request)
    {
        $id = $request->query('id');
        $user_id = $request->header('id');

        $supplier = Supplier::where('user_id', $user_id)->where('id', $id)->first();
        $brands = Brand::select('id', 'name')->get();

        return Inertia::render('Dashboard/Supplier/SupplierSavePage', [
            'supplier' => $supplier, // Pass customer as key-value pair
            'brands' => $brands,
        ]);
    }

    //====================create supplier=====================//
    public function storeSupplier(Request $request)
    {
        $user_id = $request->header('id');

        $request->validate([
            'name' => 'required|max:50',
            'email' => 'required|email|max:50|unique:suppliers',
            'phone' => 'required|numeric|digits_between:11,20',
            'address' => 'required|string|max:255',
        ]);

        try {
            Supplier::create([
                'name' => $request->input('name'),
                'email' => $request->input('email'),
                'phone' => $request->input('phone'),
                'address' => $request->input('address'),
                'user_id' => $user_id,
                'brand_id' => $request->input('brand_id'),
            ]);

            $data = ['message' => 'Supplier created successfully', 'status' => true, 'code' => 201];
            return to_route('supplier.page')->with($data);
        } catch (Exception $e) {
            $data = ['message' => 'Fail to supplier create', 'status' => false, 'code' => 201];
            return redirect()->back()->with($data);
        }
    }

    //====================show supplier page=====================//
    public function updateSupplier(Request $request, $id)
    {
        $user_id = $request->header('id');

        // Retrieve supplier by ID and user
        $supplier = Supplier::where('user_id', $user_id)->where('id', $id)->firstOrFail();

        // Validate input
        $request->validate([
            'name' => 'required|string|max:50',
            'email' => 'required|email|max:50|unique:suppliers,email,' . $id,
            'phone' => 'required|numeric|digits_between:11,20|unique:suppliers,phone,' . $id,
            'address' => 'required|string|max:255',
            'brand_id' => 'required|exists:brands,id',
        ]);

        try {
            // Update supplier
            $supplier->update([
                'user_id' => $user_id,
                'name' => $request->input('name'),
                'email' => $request->input('email'),
                'phone' => $request->input('phone'),
                'address' => $request->input('address'),
                'brand_id' => $request->input('brand_id'),
            ]);

            $data = ['message' => 'Supplier updated successfully', 'status' => true, 'code' => 201];
            return to_route('supplier.page')->with($data);
        } catch (Exception $e) {
            $data = ['message' => 'Failed to update supplier', 'status' => false, 'code' => 500];
            return redirect()->back()->with($data);
        }
    }

    //====================show supplier page=====================//
    public function deleteSupplier(Request $request, $id)
    {
        $user_id = $request->header('id');

        $supplier = Supplier::where('user_id', $user_id)->where('id', $id)->delete();

        if ($supplier) {
            $data = ['message' => 'Supplier deleted successfully', 'status' => true, 'code' => 201];
            return redirect()->back()->with($data);
        } else {
            $data = ['message' => 'Failed to delete supplier', 'status' => false, 'code' => 500];
            return redirect()->back()->with($data);
        }
    }

    //====================suppllier details=======================//
     public function supplierDetails(Request $request, $id)
    {
        $user_id = $request->header('id');
        $supplier = Supplier::with('brand')->with('invoices')->where('user_id', $user_id)->where('id', $id)->first();
        $transectionWithSupplier = PurchaseInvoice::where('user_id', $user_id)->where('supplier_id', $id)->sum('account_payable');
        return response()->json([
            'supplier' => $supplier,
            'transectionWithSupplier' => $transectionWithSupplier,
        ]);
    }
}
