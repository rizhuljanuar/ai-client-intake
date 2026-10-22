<?php

namespace Tests\Feature;

use App\Enums\ScopeVersionSource;
use App\Enums\ScopeVersionStatus;
use App\Models\ClientContact;
use App\Models\IntakeSubmission;
use App\Models\ScopeVersion;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReviewerEndpointAuthorizationTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_cannot_submit_a_decision(): void
    {
        [$submission, $version] = $this->makeReviewVersion();

        $response = $this->post(route('reviewer.submissions.versions.approve', [
            'submission' => $submission->id,
            'version' => $version->id,
        ]), ['note' => 'Scope sudah diperiksa dengan teliti.']);

        $response->assertRedirect(route('login'));
        $this->assertDatabaseCount('approval_decisions', 0);
    }

    public function test_assigned_reviewer_can_approve_version(): void
    {
        [$submission, $version] = $this->makeReviewVersion();
        $reviewer = User::factory()->create();
        $submission->reviewers()->attach($reviewer->id);

        $response = $this->actingAs($reviewer)->post(
            route('reviewer.submissions.versions.approve', [
                'submission' => $submission->id,
                'version' => $version->id,
            ]),
            ['note' => 'Scope sudah diperiksa terhadap intake.'],
        );

        $response->assertRedirect(route('reviewer.submissions.review.show', [
            'submission' => $submission->id,
            'version' => $version->id,
        ]));
        $this->assertDatabaseHas('approval_decisions', [
            'scope_version_id' => $version->id,
            'decision' => 'approved',
            'decided_by_id' => $reviewer->id,
        ]);
    }

    public function test_user_not_assigned_to_submission_cannot_decide(): void
    {
        [$submission, $version] = $this->makeReviewVersion();
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post(
            route('reviewer.submissions.versions.approve', [
                'submission' => $submission->id,
                'version' => $version->id,
            ]),
            ['note' => 'Scope sudah diperiksa terhadap intake.'],
        );

        $response->assertForbidden();
        $this->assertDatabaseCount('approval_decisions', 0);
    }

    public function test_version_from_another_submission_is_not_bound(): void
    {
        [$submissionA] = $this->makeReviewVersion();
        [$submissionB, $versionB] = $this->makeReviewVersion();
        $reviewer = User::factory()->create();
        $submissionA->reviewers()->attach($reviewer->id);

        $response = $this->actingAs($reviewer)->post(
            route('reviewer.submissions.versions.approve', [
                'submission' => $submissionA->id,
                'version' => $versionB->id,
            ]),
            ['note' => 'Scope sudah diperiksa terhadap intake.'],
        );

        $response->assertNotFound();
        $this->assertDatabaseCount('approval_decisions', 0);
        $this->assertSame(
            ScopeVersionStatus::ReadyForApproval,
            $versionB->fresh()->status,
        );
    }

    public function test_assigned_reviewer_cannot_decide_version_that_is_not_ready(): void
    {
        [$submission, $version] = $this->makeReviewVersion(
            ScopeVersionStatus::Draft,
        );
        $reviewer = User::factory()->create();
        $submission->reviewers()->attach($reviewer->id);

        $response = $this->actingAs($reviewer)->post(
            route('reviewer.submissions.versions.approve', [
                'submission' => $submission->id,
                'version' => $version->id,
            ]),
            ['note' => 'Scope sudah diperiksa terhadap intake.'],
        );

        $response->assertForbidden();
        $this->assertDatabaseCount('approval_decisions', 0);
    }

    private function makeReviewVersion(
        ScopeVersionStatus $status = ScopeVersionStatus::ReadyForApproval,
    ): array {
        $contact = ClientContact::create([
            'name' => 'Kontak Data Uji',
            'email' => fake()->unique()->safeEmail(),
            'organization_name' => 'Organisasi Data Uji',
        ]);

        $submission = IntakeSubmission::create([
            'client_contact_id' => $contact->id,
            'contact_name_snapshot' => $contact->name,
            'contact_email_snapshot' => $contact->email,
            'organization_name_snapshot' => $contact->organization_name,
            'status' => 'draft_ready',
            'submitted_at' => now(),
        ]);

        $version = ScopeVersion::create([
            'intake_submission_id' => $submission->id,
            'version_number' => 1,
            'status' => $status,
            'source_type' => ScopeVersionSource::Reviewer,
            'snapshot' => ['summary' => 'Scope sintetis untuk test.'],
        ]);

        return [$submission, $version];
    }
}
