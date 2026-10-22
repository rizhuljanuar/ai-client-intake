<?php

namespace Tests\Feature;

use App\Enums\IntakeSubmissionStatus;
use App\Models\ClientContact;
use App\Models\IntakeSubmission;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReviewerWorkspaceTest extends TestCase
{
    use RefreshDatabase;

    public function test_workspace_shows_source_data_and_empty_workflow_panels(): void
    {
        $submission = $this->createSubmission();

        $response = $this->get(route('reviewer.submissions.review.show', $submission));

        $response->assertOk();
        $response->assertSee('Data dari calon klien');
        $response->assertSee('Belum ada draft atau revision yang dapat ditampilkan.');
        $response->assertSee('Belum ada pertanyaan yang dipilih untuk workflow.');
        $response->assertSee('Belum ada risiko yang disimpan sebagai item workflow.');
        $response->assertSee('Belum ada asumsi yang disimpan sebagai item workflow.');
        $response->assertDontSee('Approve');
        $response->assertDontSee('Reject');
    }

    private function createSubmission(): IntakeSubmission
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
            'problem_statement' => 'Reviewer perlu membaca data sumber.',
            'desired_outcome' => 'Reviewer dapat menelusuri asal perubahan.',
        ]);

        return $submission;
    }
}
