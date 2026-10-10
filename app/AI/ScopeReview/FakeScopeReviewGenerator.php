<?php

namespace App\AI\ScopeReview;

use Carbon\CarbonImmutable;

final class FakeScopeReviewGenerator implements ScopeReviewGenerator
{
    public function __construct(
        private readonly ScopeReviewOutputValidator $validator,
    ) {
    }

    public function generate(ScopeReviewInput $input): ScopeReviewDraft
    {
        $payload = [
            'contract_version' => 'scope_review.v1',
            'source_submission_id' => $input->submissionId,
            'summary' => sprintf(
                'Kebutuhan proyek berfokus pada masalah berikut: %s',
                $input->problemStatement,
            ),
            'unclear_items' => [],
            'risks' => [],
            'assumptions' => [
                [
                    'id' => 'assumption-1',
                    'statement' => 'Reviewer manusia memeriksa draft sebelum keputusan scope dibuat.',
                    'source_fields' => [],
                    'needs_confirmation' => true,
                ],
            ],
            'follow_up_questions' => [],
            'review_notes' => [],
            'generation' => [
                'provider_mode' => 'fake',
                'generated_at' => CarbonImmutable::now()->toIso8601String(),
                'prompt_revision' => 'scope-review-prompt.v1',
            ],
        ];

        return $this->validator->validate($payload, $input->submissionId);
    }
}
