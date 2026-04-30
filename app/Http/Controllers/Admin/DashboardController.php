<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Customer;

class DashboardController extends Controller
{
    public function index()
    {
        // protect admin page - TEMPORARILY DISABLED FOR TESTING
        // if (!session('user_id')) {
        //     return redirect('/admin/signin');
        // }

        $customerCount = Customer::count();
        $recentCustomers = Customer::orderByDesc('CustomerUpdateDate')
            ->limit(5)
            ->get();

        return view('admin.index', compact('customerCount', 'recentCustomers'));
    }
}