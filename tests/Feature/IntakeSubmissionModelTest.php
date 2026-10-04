<?php

namespace Tests\Feature;

use App\Enums\IntakeSubmissionStatus;
use App\Models\ClientContact;
use App\Models\IntakeSubmission;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class IntakeSubmissionModelTest extends TestCase
{
    use RefreshDatabase;

    public function test_submission_can_load_contact_brief_and_status_history(): void
    {
        $contact = ClientContact::create([
            'name' => 'Data Uji',
            'email' => 'data-uji@example.test',
            'organization_name' => 'Organisasi Uji',
        ]);

        $submission = IntakeSubmission::create([
            'client_contact_id' => $contact->id,
            'contact_name_snapshot' => $contact->name,
            'contact_email_snapshot' => $contact->email,
            'organization_name_snapshot' => $contact->organization_name,
            'status' => IntakeSubmissionStatus::Submitted,
            'submitted_at' => now(),
        ]);

        $submission->projectBrief()->create([
            'problem_statement' => 'Reviewer memerlukan tempat untuk menerima kebutuhan proyek.',
            'desired_outcome' => 'Reviewer dapat memeriksa kebutuhan sebelum diskusi lanjutan.',
        ]);

        $submission->statusHistories()->create([
            'from_status' => null,
            'to_status' => IntakeSubmissionStatus::Submitted,
            'reason' => 'Form intake diterima',
        ]);

        $submission->load(['clientContact', 'projectBrief', 'statusHistories']);

        $this->assertSame('Data Uji', $submission->clientContact->name);
        $this->assertSame(
            'Reviewer memerlukan tempat untuk menerima kebutuhan proyek.',
            $submission->projectBrief->problem_statement,
        );

        $this->assertSame(IntakeSubmissionStatus::Submitted, $submission->status);
        $this->assertSame(
            IntakeSubmissionStatus::Submitted,
            $submission->statusHistories->sole()->to_status,
        );
    }
}
