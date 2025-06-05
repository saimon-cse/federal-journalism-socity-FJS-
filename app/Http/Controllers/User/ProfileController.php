<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProfileController extends Controller
{
    // ... other profile methods ...

    public function memberships()
    {
        $user = Auth::user();
        // Eager load payments and their media for payment proofs
        $memberships = $user->memberships()->with('payments.media')->orderBy('created_at', 'desc')->paginate(5);
        $activeMembership = $user->activeMembership()->first();
        $latestApplication = $user->latestMembershipApplication()->with('payments.media')->first();


        return view('user.profile.memberships', compact('user', 'memberships', 'activeMembership', 'latestApplication'));
    }
}
