<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Payment;
use Spatie\Permission\Models\Role; // For assigning Member role
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Notification; // If sending notifications
// use App\Notifications\MembershipApprovedNotification; // Example
// use App\Notifications\MembershipRejectedNotification; // Example

class MembershipApplicationController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:view-memberships', ['only' => ['index', 'review']]);
        $this->middleware('permission:manage-memberships', ['only' => ['approve', 'reject']]);
    }

    public function index(Request $request): View
    {
        $query = User::whereNotNull('membership_application_status')
                     ->where('is_member', false) // Show only non-members with applications
                     ->with(['payments' => function ($q) {
                         $q->where('payable_type', User::class)->where('purpose', 'Membership Registration Fee')->latest();
                     }]);

        if ($request->filled('status')) {
            $query->where('membership_application_status', $request->status);
        } else {
            // Default to pending approval
            $query->where('membership_application_status', 'pending_approval');
        }

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $applications = $query->latest('updated_at')->paginate(15)->withQueryString();
        $statuses = ['pending_approval' => 'Pending Approval', 'pending_payment' => 'Pending Payment', 'approved' => 'Approved (Error State)', 'rejected' => 'Rejected'];


        return view('admin.membership_applications.index', compact('applications', 'statuses'));
    }

    public function review(User $user): View // User is the applicant
    {
        // Eager load the specific payment for membership registration
        $user->load(['profile', 'payments' => function ($q) {
            $q->where('payable_type', User::class)
              ->where('purpose', 'Membership Registration Fee')
              ->orderBy('created_at', 'desc');
        }]);

        $membershipPayment = $user->payments->first(); // Get the latest relevant payment

        if (!$membershipPayment && $user->membership_application_status === 'pending_approval') {
            // This case might indicate an issue or a state where payment was expected but not found
        }


        return view('admin.membership_applications.review', compact('user', 'membershipPayment'));
    }

    public function approve(Request $request, User $user): RedirectResponse
    {
        // Find the relevant payment and mark it as verified
        $payment = Payment::where('user_id', $user->id)
                           ->where('payable_type', User::class)
                           ->where('payable_id', $user->id)
                           ->where('purpose', 'Membership Registration Fee')
                           ->where('status', 'pending_verification') // Only approve if payment was pending
                           ->latest()->first();

        if (!$payment) {
            return redirect()->route('admin.membership.applications.review', $user)
                             ->with('error', 'No pending membership fee payment found for this user to verify.');
        }

        $payment->status = 'verified';
        $payment->verified_by_user_id = Auth::id();
        $payment->verified_at = now();
        $payment->save();

        // Update user to member
        $user->is_member = true;
        $user->membership_application_status = 'approved';
        $user->membership_start_date = now();
        // $user->membership_expires_on = now()->addYear(); // If annual
        $user->save();

        // Assign 'Member' role
        $memberRole = Role::where('name', 'Member')->first();
        if ($memberRole) {
            $user->assignRole($memberRole);
        } else {
            // Log error or notify admin that 'Member' role doesn't exist
        }

        // Send notification to user
        // Notification::send($user, new MembershipApprovedNotification());

        return redirect()->route('admin.membership.applications.index')->with('success', "Membership for {$user->name} approved successfully.");
    }

    public function reject(Request $request, User $user): RedirectResponse
    {
        $request->validate([
            'rejection_reason' => 'required|string|max:1000',
        ]);

        // Find the relevant payment and mark it as rejected (or handle refund process)
        $payment = Payment::where('user_id', $user->id)
                           ->where('payable_type', User::class)
                           ->where('payable_id', $user->id)
                           ->where('purpose', 'Membership Registration Fee')
                           ->where('status', 'pending_verification')
                           ->latest()->first();
        if ($payment) {
            $payment->status = 'rejected'; // Or another status like 'refund_pending'
            $payment->verification_notes = "Membership Rejected: " . $request->rejection_reason;
            $payment->verified_by_user_id = Auth::id();
            $payment->verified_at = now(); // Rejection is a form of verification
            $payment->save();
        }

        $user->is_member = false; // Ensure they are not a member
        $user->membership_application_status = 'rejected';
        $user->membership_rejection_reason = $request->rejection_reason;
        $user->save();

        // Send notification to user
        // Notification::send($user, new MembershipRejectedNotification($request->rejection_reason));

        return redirect()->route('admin.membership.applications.index')->with('success', "Membership for {$user->name} rejected.");
    }
}
