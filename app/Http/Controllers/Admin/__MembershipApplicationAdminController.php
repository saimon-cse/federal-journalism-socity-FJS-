<?php // app/Http/Controllers/Admin/MembershipApplicationAdminController.php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MembershipApplicationAdminController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:view-memberships', ['only' => ['index', 'review']]);
        // Manage-memberships permission can be used for manual overrides if needed later
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
            // Default to statuses that need attention
            $query->whereIn('membership_application_status', ['pending_approval', 'pending_payment']);
        }
        // ... (rest of index method is fine)
        $applications = $query->latest('updated_at')->paginate(15)->withQueryString();
        $statuses = [
            'pending_approval' => 'Pending Approval (Payment Submitted)',
            'pending_payment' => 'Pending Payment (Application Started)',
            'rejected' => 'Rejected',
            // 'approved' state should result in is_member=true and might not show here long
        ];
        return view('admin.membership_applications.index', compact('applications', 'statuses'));
    }

    public function review(User $user): View // User is the applicant
    {
        $user->load(['profile', 'payments' => function ($q) {
            $q->where('payable_type', User::class)
              ->where('purpose', 'Membership Registration Fee')
              ->orderBy('created_at', 'desc');
        }]);
        $membershipPayment = $user->payments->first();
        return view('admin.membership_applications.review', compact('user', 'membershipPayment'));
    }

    // Approve and Reject methods are now primarily handled by PaymentVerificationController's hooks.
    // These can be kept for manual overrides or future complex workflows if needed.
    // For now, they are not the primary action points.
    /*
    public function approve(Request $request, User $user): RedirectResponse { ... }
    public function reject(Request $request, User $user): RedirectResponse { ... }
    */
}
