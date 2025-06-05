<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Membership;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Mail\MembershipRejected;
use App\Mail\MembershipActivated; // If admin manually activates
use Carbon\Carbon;
use App\Models\MembershipType;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use App\Models\Setting; // Assuming you have a Setting model for membership types
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use App\Models\PaymentAccount; // For manual payment recording
use App\Models\FinancialLedger; // For financial records
use App\Models\FinancialTransactionCategory; // For income category creation
use App\Models\Payment;

class MembershipController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize('view-memberships');
        $query = Membership::with(['user', 'approvedBy', 'payments']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('type')) {
            $query->where('membership_type', $request->type);
        }
        if ($request->filled('user_search')) {
            $searchTerm = $request->user_search;
            $query->whereHas('user', function ($q) use ($searchTerm) {
                $q->where('name', 'like', "%{$searchTerm}%")
                    ->orWhere('email', 'like', "%{$searchTerm}%");
            });
        }

        $memberships = $query->latest()->paginate(15)->withQueryString();
        $statuses = ['pending_application', 'pending_payment', 'active', 'payment_failed', 'expired', 'cancelled', 'rejected'];
        $types = MembershipType::select('slug', 'name')->distinct()->get()->pluck('name', 'slug')->toArray();

        return view('admin.memberships.index', compact('memberships', 'statuses', 'types'));
    }

    public function show(Membership $membership)
    {
        $this->authorize('view-memberships');
        $membership->load(['user.profile', 'approvedBy', 'payments.media', 'payments.paymentMethod']);
        return view('admin.memberships.show', compact('membership'));
    }

    // If you have an explicit 'pending_application' state that admin approves to 'pending_payment'
    // public function approveApplication(Membership $membership) { ... }

    public function rejectApplication(Request $request, Membership $membership)
    {
        $this->authorize('manage-memberships');
        $request->validate(['rejection_reason' => 'required|string|max:1000']);

        if (!in_array($membership->status, ['pending_application', 'pending_payment'])) {
            return redirect()->back()->with('error', 'This application cannot be rejected at its current state.');
        }

        $membership->update([
            'status' => 'rejected',
            'remarks' => 'Application Rejected: ' . $request->rejection_reason,
            'approved_by_user_id' => Auth::id(), // User who rejected
            'approved_at' => now(), // Timestamp of rejection
        ]);

        Mail::to($membership->user->email)->send(new MembershipRejected($membership, $request->rejection_reason));

        return redirect()->route('admin.memberships.show', $membership)->with('success', 'Membership application rejected.');
    }



public function edit(Membership $membership)
{
    $this->authorize('manage-memberships');
    $allMembershipTypes = MembershipType::orderBy('name')->get();
    $statuses = ['pending_application', 'pending_payment', 'active', 'payment_failed', 'expired', 'cancelled', 'rejected'];

    // Pass the original status for JS logic
    $membership->getOriginal('status'); //will give the status as it was when fetched from DB
    return view('admin.memberships.edit', compact('membership', 'allMembershipTypes', 'statuses'));
}

    public function update(Request $request, Membership $membership)
    {
        $this->authorize('manage-memberships');
        $validated = $request->validate([
            'membership_type_id' => 'required|exists:membership_types,id', // CHANGED
            'status' => 'required|string|in:pending_application,pending_payment,active,payment_failed,expired,cancelled,rejected',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'remarks' => 'nullable|string|max:1000',
            // ADD FIELDS FOR MANUAL PAYMENT RECORDING IF ACTIVATING
            'manual_activation_payment_account_id' => 'required_if:status,active|nullable|exists:payment_accounts,id',
            'manual_activation_payment_amount' => 'required_if:status,active|nullable|numeric|min:0',
            'manual_activation_transaction_ref' => 'required_if:status,active|nullable|string|max:255',
        ]);

        $oldStatus = $membership->status;

        DB::transaction(function () use ($validated, $membership, $oldStatus, $request) {
            // Update membership type if changed
            if ($membership->membership_type_id != $validated['membership_type_id']) {
                $membership->membership_type_id = $validated['membership_type_id'];
            }
            $membership->status = $validated['status'];
            $membership->start_date = $validated['start_date'] ? Carbon::parse($validated['start_date']) : $membership->start_date;
            $membership->end_date = $validated['end_date'] ? Carbon::parse($validated['end_date']) : $membership->end_date;
            $membership->remarks = $validated['remarks'] ?? $membership->remarks;


            if ($validated['status'] === 'active' && $oldStatus !== 'active') {
                $membership->approved_by_user_id = Auth::id();
                $membership->approved_at = now();
                if (!$membership->start_date) $membership->start_date = Carbon::today();

                $membershipType = $membership->membershipType; // Get from relation
                if ($membershipType) {
                    $durationMonths = $membershipType->getDurationInMonths();
                    if ($durationMonths && !$membership->end_date) { // Only set end_date if not already manually set
                        $membership->end_date = Carbon::parse($membership->start_date)->addMonths($durationMonths)->subDay();
                    }
                }

                // If activating and it was pending_payment or similar, and admin provided payment details:
                if ($request->filled('manual_activation_payment_account_id') && $request->filled('manual_activation_payment_amount') && $request->manual_activation_payment_amount > 0) {
                    $paymentAccountForIncome = PaymentAccount::find($request->manual_activation_payment_account_id);
                    $paymentAmount = (float) $request->manual_activation_payment_amount;

                    if ($paymentAccountForIncome) {
                        // Create a Payment record for this manual activation
                        $newPayment = $membership->payments()->create([
                            'payment_uuid' => (string) Str::uuid(),
                            'user_id' => $membership->user_id,
                            'amount_due' => $paymentAmount, // Or get expected fee from membershipType
                            'amount_paid' => $paymentAmount,
                            'currency_code' => 'BDT',
                            'net_amount_payable' => $paymentAmount,
                            'payment_method_id' => null, // Or a specific "Manual Admin Entry" method
                            'manual_transaction_id_user' => $request->manual_activation_transaction_ref ?? 'ADMIN_ACTIVATED',
                            'manual_payment_datetime_user' => now(),
                            'manual_payment_to_account_id' => $paymentAccountForIncome->id,
                            'status' => 'successful', // Mark as successful since admin is doing it
                            'verified_by_user_id' => Auth::id(),
                            'verified_at' => now(),
                            'verification_remarks' => 'Manually activated by admin: ' . ($validated['remarks'] ?? ''),
                        ]);

                        // Create Ledger Entry for this payment
                        $incomeCategory = FinancialTransactionCategory::firstOrCreate(
                            ['name' => 'Membership Fee Income (Admin)', 'type' => 'income'],
                            ['description' => 'Membership fee income recorded by admin.', 'is_active' => true]
                        );
                        FinancialLedger::create([
                            'ledger_entry_uuid' => (string) Str::uuid(),
                            'transaction_datetime' => now(),
                            'entry_type' => 'income',
                            'amount' => $paymentAmount,
                            'description' => "Manual membership activation for {$membership->user->name}. Membership ID: {$membership->id}",
                            'category_id' => $incomeCategory->id,
                            'to_payment_account_id' => $paymentAccountForIncome->id,
                            'payment_id' => $newPayment->id,
                            'referenceable_id' => $membership->id,
                            'referenceable_type' => Membership::class,
                            'recorded_by_user_id' => Auth::id(),
                        ]);

                        // Update Payment Account Balance
                        $paymentAccountForIncome->increment('current_balance', $paymentAmount);
                        $membership->last_payment_date = now();
                    }
                } elseif ($oldStatus === 'pending_payment' && !$membership->last_payment_date) {
                    // If no manual payment details provided but activating from pending_payment,
                    // there should ideally be an existing verified payment.
                    // If not, this activation might be for a free tier or an overlooked payment.
                    // Log this or require admin to specify how payment was confirmed.
                    Log::info("Membership ID {$membership->id} activated from 'pending_payment' without new manual payment details. Last payment date not updated unless a related payment was already verified.");
                }


                if (!$membership->user->hasRole('Registered Member')) {
                    $membership->user->assignRole('Registered Member');
                }
                Mail::to($membership->user->email)->send(new MembershipActivated($membership));
            } elseif ($validated['status'] !== 'active' && $oldStatus === 'active') {
                // Logic for when a membership is deactivated by admin
                if ($membership->user->hasRole('Registered Member')) { // Consider conditions for role removal
                    // $membership->user->removeRole('Registered Member'); // Be cautious with role removal
                }
            }
            $membership->save();
        });

        return redirect()->route('admin.memberships.show', $membership)->with('success', 'Membership updated successfully.');
    }
}
