<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    // DISPLAY ALL CUSTOMERS
    public function index()
    {
        // protect admin page - TEMPORARILY DISABLED FOR TESTING
        // if (!session('user_id')) {
        //     return redirect('/admin/signin');
        // }

        $customers = Customer::orderBy('CustomerID', 'desc')->get();
        $customerCount = Customer::count();
        $recentCustomers = Customer::orderByDesc('CustomerUpdateDate')
            ->limit(5)
            ->get();
        return view('admin.customers.index', compact('customers', 'customerCount', 'recentCustomers'));
    }

    // SHOW CREATE FORM
    public function create()
    {
        // protect admin page - TEMPORARILY DISABLED FOR TESTING
        // if (!session('user_id')) {
        //     return redirect('/admin/signin');
        // }

        return view('admin.customers.create');
    }

    // STORE CUSTOMER (AUTO CUSTOMER CODE - IMPROVED)
    public function store(Request $request)
    {
        // protect admin page - TEMPORARILY DISABLED FOR TESTING
        // if (!session('user_id')) {
        //     return redirect('/admin/signin');
        // }
        $request->validate([
            'CustomerName' => 'required|string|max:255',
            'CustomerEmail' => 'nullable|email|max:255',
            'CustomerContactNumber' => 'nullable|string|max:50',
            'CustomerAddressLine1' => 'nullable|string|max:255',
            'CustomerCity' => 'nullable|string|max:100',
            'CustomerProvince' => 'nullable|string|max:100',
        ]);

        // SAFE AUTO CODE (NO RELIANCE ON ID)
        $lastCustomer = Customer::orderBy('CustomerID', 'desc')->first();

        $nextNumber = $lastCustomer
            ? ((int) substr($lastCustomer->CustomerCode, -4)) + 1
            : 1;

        $customerCode = 'CUST-' . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);

        Customer::create([
            'CustomerCode' => $customerCode,
            'CustomerName' => $request->CustomerName,
            'CustomerEmail' => $request->CustomerEmail,
            'CustomerContactNumber' => $request->CustomerContactNumber,
            'CustomerAddressLine1' => $request->CustomerAddressLine1,
            'CustomerCity' => $request->CustomerCity,
            'CustomerProvince' => $request->CustomerProvince,
            'CustomerUpdateDate' => now(),
        ]);

        return redirect()->route('admin.customers.index')
            ->with('success', 'Customer created successfully!');
    }

    // SHOW EDIT FORM
    public function edit($id)
    {
        // protect admin page - TEMPORARILY DISABLED FOR TESTING
        // if (!session('user_id')) {
        //     return redirect('/admin/signin');
        // }

        $customer = Customer::findOrFail($id);
        return view('admin.customers.edit', compact('customer'));
    }

    // UPDATE CUSTOMER
    public function update(Request $request, $id)
    {
        // protect admin page - TEMPORARILY DISABLED FOR TESTING
        // if (!session('user_id')) {
        //     return redirect('/admin/signin');
        // }
        $request->validate([
            'CustomerName' => 'required|string|max:255',
            'CustomerEmail' => 'nullable|email|max:255',
            'CustomerContactNumber' => 'nullable|string|max:50',
            'CustomerAddressLine1' => 'nullable|string|max:255',
            'CustomerCity' => 'nullable|string|max:100',
            'CustomerProvince' => 'nullable|string|max:100',
        ]);

        $customer = Customer::findOrFail($id);

        $customer->update([
            'CustomerName' => $request->CustomerName,
            'CustomerEmail' => $request->CustomerEmail,
            'CustomerContactNumber' => $request->CustomerContactNumber,
            'CustomerAddressLine1' => $request->CustomerAddressLine1,
            'CustomerCity' => $request->CustomerCity,
            'CustomerProvince' => $request->CustomerProvince,
            'CustomerUpdateDate' => now(),
        ]);

        return redirect()->route('admin.customers.index')
            ->with('success', 'Customer updated successfully!');
    }

    // SHOW CUSTOMER DETAILS
    public function show($id)
    {
        // protect admin page - TEMPORARILY DISABLED FOR TESTING
        // if (!session('user_id')) {
        //     return redirect('/admin/signin');
        // }

        $customer = Customer::findOrFail($id);
        return view('admin.customers.show', compact('customer'));
    }

    // DELETE CUSTOMER
    public function destroy($id)
    {
        // protect admin page - TEMPORARILY DISABLED FOR TESTING
        // if (!session('user_id')) {
        //     return redirect('/admin/signin');
        // }
        Customer::findOrFail($id)->delete();

        return redirect()->route('admin.customers.index')
            ->with('success', 'Customer deleted successfully!');
    }
}