<?php

namespace Tests\Feature;

use App\Enums\IntakeSubmissionStatus;
use App\Models\IntakeSubmission;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StoreIntakeSubmissionTest extends TestCase
{
    use RefreshDatabase;

    public function test_valid_intake_is_saved_and_redirects_to_result(): void
    {
        $payload = [
            'contact_name' => 'Data Uji',
            'contact_email' => 'data-uji@example.test',
            'organization_name' => 'Organisasi Uji',
            'problem_statement' => 'Reviewer belum memiliki tempat terpusat untuk menerima kebutuhan proyek.',
            'desired_outcome' => 'Reviewer dapat memeriksa kebutuhan sebelum diskusi lanjutan.',
            'constraints' => 'Gunakan data sintetis selama pengembangan lokal.',
            'additional_context' => 'Submission ini hanya dipakai untuk pengujian.',
        ];

        $response = $this->post(route('intake.store'), $payload);

        $submission = IntakeSubmission::query()->sole();

        $response->assertRedirect(route('intake.submitted', $submission));

        $this->assertDatabaseHas('client_contacts', [
            'email' => 'data-uji@example.test',
        ]);

        $this->assertDatabaseHas('project_briefs', [
            'intake_submission_id' => $submission->id,
            'problem_statement' => $payload['problem_statement'],
        ]);

        $this->assertSame(IntakeSubmissionStatus::Submitted, $submission->status);

        $this->assertDatabaseHas('submission_status_histories', [
            'intake_submission_id' => $submission->id,
            'from_status' => null,
            'to_status' => IntakeSubmissionStatus::Submitted->value,
        ]);
    }

    public function test_invalid_intake_is_rejected_without_creating_records(): void
    {
        $response = $this->from(route('intake.create'))
            ->post(route('intake.store'), [
                'contact_name' => '',
                'contact_email' => 'alamat-tidak-valid',
                'problem_statement' => 'terlalu pendek',
                'desired_outcome' => '',
            ]);

        $response->assertRedirect(route('intake.create'));

        $response->assertSessionHasErrors([
            'contact_name',
            'contact_email',
            'problem_statement',
            'desired_outcome',
        ]);

        $this->assertDatabaseCount('client_contacts', 0);
        $this->assertDatabaseCount('intake_submissions', 0);
        $this->assertDatabaseCount('project_briefs', 0);
        $this->assertDatabaseCount('submission_status_histories', 0);
    }
}
