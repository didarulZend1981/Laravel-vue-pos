<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\SaleInvoice;
use Exception;
use Illuminate\Http\Request;
use Inertia\Inertia;

class CustomerController extends Controller
{
    //===========================customer page ==========================//
    public function showCustomer(Request $request)
    {
        $user_id = $request->header('id');

        $customers = Customer::where('user_id', $user_id)->orderBy('id', 'desc')->get();
        return Inertia::render('Dashboard/Customer/CustomerPage', [
            'customers' => $customers,
        ]);
    }

    //=================customer create and update page=====================//
    public function showSaveCustomer(Request $request)
    {
        $id = $request->query('id');
        $user_id = $request->header('id');

        $customer = Customer::where('user_id', $user_id)->where('id', $id)->first();

        return Inertia::render('Dashboard/Customer/CustomerSavePage', [
            'customer' => $customer, // Pass customer as key-value pair
        ]);
    }

    //===========================create customer ==========================//
    public function storeCustomer(Request $request)
    {
        $user_id = $request->header('id');

        $request->validate([
            'name' => 'required|max:100',
            'email' => 'required|string|email|max:50|unique:customers',
            'phone' => 'required|numeric|digits_between:11,20',
            'zip' => 'nullable|string|digits_between:4,12',
            'address' => 'nullable|string|max:255',
            'comment' => 'nullable|string|max:255',
        ]);

        try {
            Customer::create([
                'name' => $request->input('name'),
                'email' => $request->input('email'),
                'phone' => $request->input('phone'),
                'zip' => $request->input('zip'),
                'address' => $request->input('address'),
                'comment' => $request->input('comment'),
                'user_id' => $user_id,
            ]);

            $data = ['message' => 'Customer created successfully', 'status' => true, 'code' => 201];
            return to_route('customer.page')->with($data);
        } catch (Exception $e) {
            $data = ['message' => 'Error creating customer', 'status' => false, 'code' => 500];
            return redirect()->back()->with($data);
        }
    }

    //===========================update customer ==========================//
    public function updateCustomer(Request $request, $id)
    {
        $user_id = $request->header('id');

        // Fetch the customer to update
        $customer = Customer::where('user_id', $user_id)->where('id', $id)->first();

        // Validate request data
        $request->validate([
            'name' => 'required|max:100',
            'email' => 'required|string|email|max:50|unique:customers,email,' . $customer->id,
            'phone' => 'required|numeric|digits_between:11,20',
            'zip' => 'nullable|string|digits_between:4,12',
            'address' => 'nullable|string|max:255',
            'comment' => 'nullable|string|max:255',
        ]);

        try {
            // Update customer details
            $customer->update([
                'name' => $request->input('name'),
                'email' => $request->input('email'),
                'phone' => $request->input('phone'),
                'zip' => $request->input('zip'),
                'address' => $request->input('address'),
                'comment' => $request->input('comment'),
            ]);

            $data = ['message' => 'Customer updated successfully', 'status' => true, 'code' => 201];
            return to_route('customer.page')->with($data);
        } catch (Exception $e) {
            $data = ['message' => 'Error updating customer', 'status' => false, 'code' => 500];
            return redirect()->back()->with($data);
        }
    }

    //============================delete customer ==========================//
    public function deleteCustomer(Request $request, $id)
    {
        $user_id = $request->header('id');
        $deleted = Customer::where('user_id', $user_id)->where('id', $id)->delete();

        if ($deleted) {
            return redirect()
                ->back()
                ->with([
                    'message' => 'Customer deleted successfully',
                    'status' => true,
                ]);
        }

        return redirect()
            ->back()
            ->with([
                'message' => 'Customer not found',
                'status' => false,
            ]);
    }

    //============================customer details ==========================//
    public function customerDetails(Request $request, $id)
    {
        $user_id = $request->header('id');
        $customer = Customer::with('invoices')->where('user_id', $user_id)->where('id', $id)->first();
        $customerTotalPayableAmount = SaleInvoice::where('user_id', $user_id)->where('customer_id', $id)->sum('customer_payable');
        return response()->json([
            'customer' => $customer,
            'customerTotalPayableAmount' => $customerTotalPayableAmount,
        ]);
    }
}
