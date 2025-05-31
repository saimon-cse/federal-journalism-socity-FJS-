<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        // You can pass data to the dashboard view here
        // For example, counts of users, pending applications, etc.
        $data = [
            'totalUsers' => \App\Models\User::count(), // Example
            // Add more stats as needed
        ];
        return view('admin.dashboard', $data);
    }
}
