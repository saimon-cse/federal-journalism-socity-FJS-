<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens; // For API authentication
use Spatie\Permission\Traits\HasRoles; // Spatie Permissions
use Spatie\MediaLibrary\HasMedia; // Spatie Media Library
use Spatie\MediaLibrary\InteractsWithMedia; // Spatie Media Library
use Illuminate\Database\Eloquent\SoftDeletes;

class User extends Authenticatable implements HasMedia, MustVerifyEmail // Added MustVerifyEmail
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles, InteractsWithMedia, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'profile_photo_path', // If not using Spatie Media for this specific one
        'email_verified_at',
        'is_active',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'is_active' => 'boolean',
        'password' => 'hashed', // For Laravel 10+
    ];

    // Spatie Media Library Collections
    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('profile_photos')->singleFile();
        $this->addMediaCollection('nid_documents')->singleFile();
        $this->addMediaCollection('passport_documents')->singleFile();
        // Add other collections as needed, e.g., CVs for job applications if stored directly on user
    }

    // Relationships
    public function profile()
    {
        return $this->hasOne(UserProfile::class);
    }

    public function addresses()
    {
        return $this->hasMany(UserAddress::class);
    }

    public function educations()
    {
        return $this->hasMany(UserEducation::class);
    }

    public function experiences()
    {
        return $this->hasMany(UserExperience::class);
    }

    public function socialMedias()
    {
        return $this->hasMany(UserSocialMedia::class);
    }

// ... existing User model code ...
public function memberships()
{
    return $this->hasMany(Membership::class);
}

public function activeMembership()
{
    return $this->hasOne(Membership::class)->where('status', 'active')->latestOfMany();
}

public function latestMembershipApplication()
{
    return $this->hasOne(Membership::class)->latestOfMany();
}
// ...

    public function paymentsMade()
    {
        return $this->hasMany(Payment::class, 'user_id');
    }

    public function paymentsVerified()
    {
        return $this->hasMany(Payment::class, 'verified_by_user_id');
    }

    public function financialLedgersRecorded()
    {
        return $this->hasMany(FinancialLedger::class, 'recorded_by_user_id');
    }

    public function financialLedgersReconciled()
    {
        return $this->hasMany(FinancialLedger::class, 'reconciled_by_user_id');
    }

    public function committeesCreated()
    {
        return $this->hasMany(Committee::class, 'created_by_user_id');
    }

    public function committeePositionsCreated()
    {
        return $this->hasMany(CommitteePosition::class, 'created_by');
    }

    public function committeeMemberships()
    {
        return $this->hasMany(CommitteeMember::class);
    }

     public function electionsCreated()
    {
        return $this->hasMany(Election::class, 'created_by');
    }

    public function electionNominationsAsCandidate()
    {
        return $this->hasMany(ElectionNomination::class, 'user_id');
    }

    public function electionNominationsProcessed()
    {
        return $this->hasMany(ElectionNomination::class, 'processed_by');
    }

    public function allowanceApplications()
    {
        return $this->hasMany(AllowanceApplication::class, 'user_id');
    }

    public function allowanceApplicationsReviewed()
    {
        return $this->hasMany(AllowanceApplication::class, 'reviewed_by');
    }

     public function allowanceApplicationsApproved()
    {
        return $this->hasMany(AllowanceApplication::class, 'approved_by');
    }

    public function allowanceApplicationDocumentsVerified()
    {
        return $this->hasMany(AllowanceApplicationDocument::class, 'verified_by');
    }

    public function allowancePaymentsProcessed() {
        return $this->hasMany(AllowancePayment::class, 'processed_by');
    }

    public function allowanceApplicationReviewsMade()
    {
        return $this->hasMany(AllowanceApplicationReview::class, 'reviewer_id');
    }

    public function allowanceApplicationLogsChanged()
    {
        return $this->hasMany(AllowanceApplicationLog::class, 'changed_by');
    }

    public function announcementsPosted()
    {
        return $this->hasMany(Announcement::class, 'posted_by');
    }

    public function announcementsTargeted()
    {
        return $this->belongsToMany(Announcement::class, 'announcement_user');
    }

    public function complaintsSubmitted()
    {
        return $this->hasMany(ComplaintSuggestion::class, 'user_id');
    }

    public function complaintsResolved()
    {
        return $this->hasMany(ComplaintSuggestion::class, 'resolved_by_user_id');
    }

    public function complaintReplies()
    {
        return $this->hasMany(ComplaintSuggestionReply::class, 'user_id');
    }

    public function jobPostingsPosted()
    {
        return $this->hasMany(JobPosting::class, 'posted_by');
    }

    public function jobApplications()
    {
        return $this->hasMany(JobApplication::class);
    }

    public function trainingsCreated()
    {
        return $this->hasMany(Training::class, 'created_by_user_id');
    }

    public function resourcePersonProfile()
    {
        return $this->hasOne(ResourcePerson::class);
    }

    public function resourcePersonContentsReviewed()
    {
        return $this->hasMany(ResourcePersonContent::class, 'reviewed_by_user_id');
    }

    public function trainingSessionsInstructed()
    {
        return $this->hasMany(TrainingSession::class, 'instructor_id');
    }

     public function trainingInstructedAs() {
        return $this->hasMany(TrainingInstructor::class, 'instructor_id');
    }

    public function trainingRegistrations()
    {
        return $this->hasMany(TrainingRegistration::class);
    }

    public function trainingRegistrationsApproved()
    {
        return $this->hasMany(TrainingRegistration::class, 'approved_by_user_id');
    }

    public function trainingInvitationsSent()
    {
        return $this->hasMany(TrainingInvitation::class, 'invited_by');
    }

    public function trainingInvitationsReceived()
    {
        return $this->hasMany(TrainingInvitation::class, 'resource_person_id');
    }
}
