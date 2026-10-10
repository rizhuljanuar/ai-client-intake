<?php

namespace Tests\Feature;

use App\AI\ScopeReview\FakeScopeReviewGenerator;
use App\AI\ScopeReview\ScopeReviewInput;
use App\AI\ScopeReview\ScopeReviewOutputValidator;
use App\AI\ScopeReview\ScopeReviewValidationException;
use Tests\TestCase;

class ScopeReviewGeneratorTest extends TestCase
{
    public function test_fake_provider_returns_the_scope_review_contract(): void
    {
        $generator = new FakeScopeReviewGenerator(
            new ScopeReviewOutputValidator(),
        );

        $draft = $generator->generate(new ScopeReviewInput(
            submissionId: 42,
            organizationName: 'Organisasi Uji',
            problemStatement: 'Reviewer membutuhkan tempat terpusat untuk menerima kebutuhan proyek.',
            desiredOutcome: 'Reviewer dapat memeriksa kebutuhan sebelum diskusi lanjutan.',
            constraints: 'Gunakan data sintetis.',
            additionalContext: null,
        ));

        $payload = $draft->toArray();

        $this->assertSame('scope_review.v1', $payload['contract_version']);
        $this->assertSame(42, $payload['source_submission_id']);
        $this->assertNotSame('', $payload['summary']);
        $this->assertIsArray($payload['unclear_items']);
        $this->assertIsArray($payload['risks']);
        $this->assertIsArray($payload['assumptions']);
        $this->assertSame('fake', $payload['generation']['provider_mode']);
    }

    public function test_validator_rejects_a_payload_for_another_submission(): void
    {
        $validator = new ScopeReviewOutputValidator();

        $this->expectException(ScopeReviewValidationException::class);

        $validator->validate([
            'contract_version' => 'scope_review.v1',
            'source_submission_id' => 99,
            'summary' => 'Ringkasan data uji.',
            'unclear_items' => [],
            'risks' => [],
            'assumptions' => [],
            'follow_up_questions' => [],
            'review_notes' => [],
            'generation' => [
                'provider_mode' => 'fake',
                'generated_at' => '2026-10-01T00:00:00Z',
                'prompt_revision' => 'scope-review-prompt.v1',
            ],
        ], 42);
    }

    public function test_validator_rejects_unknown_risk_severity(): void
    {
        $validator = new ScopeReviewOutputValidator();

        try {
            $validator->validate([
                'contract_version' => 'scope_review.v1',
                'source_submission_id' => 42,
                'summary' => 'Ringkasan data uji.',
                'unclear_items' => [],
                'risks' => [[
                    'id' => 'risk-1',
                    'title' => 'Risiko data uji',
                    'description' => 'Alasan risiko data uji.',
                    'severity' => 'urgent',
                    'source_fields' => [],
                    'mitigation' => 'Konfirmasi melalui reviewer.',
                ]],
                'assumptions' => [],
                'follow_up_questions' => [],
                'review_notes' => [],
                'generation' => [
                    'provider_mode' => 'fake',
                    'generated_at' => '2026-10-01T00:00:00Z',
                    'prompt_revision' => 'scope-review-prompt.v1',
                ],
            ], 42);
        } catch (ScopeReviewValidationException $exception) {
            $this->assertArrayHasKey('risks.0.severity', $exception->errors);

            return;
        }

        $this->fail('Validator seharusnya menolak severity yang tidak dikenal.');
    }
}
