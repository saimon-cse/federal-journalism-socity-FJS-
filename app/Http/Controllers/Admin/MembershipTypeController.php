<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MembershipType;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class MembershipTypeController extends Controller
{
    public function __construct()
    {
        // Add appropriate permission checks, e.g., 'manage-membership-types'
        // $this->middleware('can:manage-membership-types');
    }

    public function index()
    {
        $this->authorize('view-memberships'); // Or a more specific 'manage-membership-types'
        $membershipTypes = MembershipType::latest()->paginate(10);
        return view('admin.membership_types.index', compact('membershipTypes'));
    }

    public function create()
    {
        $this->authorize('manage-memberships');
        return view('admin.membership_types.create');
    }

    public function store(Request $request)
    {
        $this->authorize('manage-memberships');
        $request->validate([
            'name' => 'required|string|max:255|unique:membership_types,name',
            'description' => 'nullable|string',
            'monthly_amount' => 'nullable|numeric|min:0|required_without:annual_amount',
            'annual_amount' => 'nullable|numeric|min:0|required_without:monthly_amount',
            'is_recurring' => 'sometimes|boolean',
            'membership_duration' => 'nullable|string|max:100', // e.g., "12 months", "Lifetime"
            'is_active' => 'sometimes|boolean',
        ]);

        MembershipType::create([
            'name' => $request->name,
            'slug' => Str::slug($request->name),
            'description' => $request->description,
            'monthly_amount' => $request->monthly_amount,
            'annual_amount' => $request->annual_amount,
            'is_recurring' => $request->has('is_recurring'),
            'membership_duration' => $request->membership_duration,
            'is_active' => $request->has('is_active'),
        ]);

        return redirect()->route('admin.membership-types.index')->with('success', 'Membership Type created successfully.');
    }

    public function edit(MembershipType $membershipType)
    {
        $this->authorize('manage-memberships');
        return view('admin.membership_types.edit', compact('membershipType'));
    }

    public function update(Request $request, MembershipType $membershipType)
    {
        $this->authorize('manage-memberships');
        $request->validate([
            'name' => 'required|string|max:255|unique:membership_types,name,' . $membershipType->id,
            'description' => 'nullable|string',
            'monthly_amount' => 'nullable|numeric|min:0|required_without:annual_amount',
            'annual_amount' => 'nullable|numeric|min:0|required_without:monthly_amount',
            'is_recurring' => 'sometimes|boolean',
            'membership_duration' => 'nullable|string|max:100',
            'is_active' => 'sometimes|boolean',
        ]);

        $membershipType->update([
            'name' => $request->name,
            'slug' => Str::slug($request->name),
            'description' => $request->description,
            'monthly_amount' => $request->monthly_amount,
            'annual_amount' => $request->annual_amount,
            'is_recurring' => $request->has('is_recurring'),
            'membership_duration' => $request->membership_duration,
            'is_active' => $request->has('is_active'),
        ]);

        return redirect()->route('admin.membership-types.index')->with('success', 'Membership Type updated successfully.');
    }

    public function destroy(MembershipType $membershipType)
    {
        $this->authorize('manage-memberships');
        // Check if type is in use by any membership
        if ($membershipType->memberships()->exists()) {
            return redirect()->route('admin.membership-types.index')->with('error', 'Cannot delete type. It is currently assigned to memberships.');
        }
        $membershipType->delete();
        return redirect()->route('admin.membership-types.index')->with('success', 'Membership Type deleted successfully.');
    }
}
