<?php

namespace Tests\Feature;

use App\Enums\ScopeVersionSource;
use App\Enums\ScopeVersionStatus;
use App\Models\ClientContact;
use App\Models\IntakeSubmission;
use App\Models\ScopeVersion;
use App\Models\User;
use App\Services\ScopeApprovalService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ScopeApprovalServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_approver_can_approve_a_ready_version_with_a_note(): void
    {
        $submission = $this->createSubmission();
        $version = ScopeVersion::create([
            'intake_submission_id' => $submission->id,
            'version_number' => 1,
            'status' => ScopeVersionStatus::ReadyForApproval,
            'source_type' => ScopeVersionSource::Reviewer,
            'snapshot' => ['summary' => 'Scope data uji.'],
        ]);
        $approver = User::factory()->create();

        $decision = (new ScopeApprovalService())->approve(
            version: $version,
            approver: $approver,
            note: 'Scope sudah diperiksa terhadap data intake.',
        );

        $version->refresh();
        $submission->refresh();

        $this->assertSame(ScopeVersionStatus::Approved, $version->status);
        $this->assertSame($version->id, $submission->active_scope_version_id);
        $this->assertSame('approved', $decision->decision->value);
        $this->assertSame('Scope sudah diperiksa terhadap data intake.', $decision->note);
        $this->assertDatabaseHas('approval_decisions', [
            'scope_version_id' => $version->id,
            'decision' => 'approved',
        ]);
    }

    public function test_reviewer_cannot_approve_without_a_note(): void
    {
        $submission = $this->createSubmission();
        $version = ScopeVersion::create([
            'intake_submission_id' => $submission->id,
            'version_number' => 1,
            'status' => ScopeVersionStatus::ReadyForApproval,
            'source_type' => ScopeVersionSource::Reviewer,
            'snapshot' => ['summary' => 'Scope data uji.'],
        ]);

        $this->expectException(\LogicException::class);

        (new ScopeApprovalService())->approve(
            version: $version,
            approver: User::factory()->create(),
            note: '   ',
        );
    }

    public function test_rejected_version_remains_history_and_does_not_become_active(): void
    {
        $submission = $this->createSubmission();
        $version = ScopeVersion::create([
            'intake_submission_id' => $submission->id,
            'version_number' => 1,
            'status' => ScopeVersionStatus::ReadyForApproval,
            'source_type' => ScopeVersionSource::Reviewer,
            'snapshot' => ['summary' => 'Scope perlu diperbaiki.'],
        ]);

        $decision = (new ScopeApprovalService())->reject(
            version: $version,
            approver: User::factory()->create(),
            note: 'Batasan akses belum dijelaskan.',
        );

        $version->refresh();
        $submission->refresh();

        $this->assertSame(ScopeVersionStatus::Rejected, $version->status);
        $this->assertNull($submission->active_scope_version_id);
        $this->assertSame('rejected', $decision->decision->value);
        $this->assertDatabaseHas('approval_decisions', [
            'scope_version_id' => $version->id,
            'decision' => 'rejected',
            'note' => 'Batasan akses belum dijelaskan.',
        ]);
    }

    public function test_approval_service_rejects_a_version_that_is_already_approved(): void
    {
        $submission = $this->createSubmission();
        $version = ScopeVersion::create([
            'intake_submission_id' => $submission->id,
            'version_number' => 1,
            'status' => ScopeVersionStatus::Approved,
            'source_type' => ScopeVersionSource::Reviewer,
            'snapshot' => ['summary' => 'Scope yang sudah final.'],
        ]);

        $this->expectException(\LogicException::class);

        (new ScopeApprovalService())->approve(
            version: $version,
            approver: User::factory()->create(),
            note: 'Percobaan keputusan kedua.',
        );
    }

    private function createSubmission(): IntakeSubmission
    {
        $contact = ClientContact::create([
            'name' => 'Data Uji',
            'email' => 'data-uji@example.test',
            'organization_name' => 'Organisasi Uji',
        ]);

        return IntakeSubmission::create([
            'client_contact_id' => $contact->id,
            'contact_name_snapshot' => $contact->name,
            'contact_email_snapshot' => $contact->email,
            'organization_name_snapshot' => $contact->organization_name,
            'status' => 'draft_ready',
            'submitted_at' => now(),
        ]);
    }
}
