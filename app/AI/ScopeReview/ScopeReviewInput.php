<?php

namespace App\AI\ScopeReview;

final readonly class ScopeReviewInput
{
    public function __construct(
        public int $submissionId,
        public ?string $organizationName,
        public string $problemStatement,
        public string $desiredOutcome,
        public ?string $constraints,
        public ?string $additionalContext,
    ) {
    }

    public function toPromptContext(): array
    {
        return [
            'submission_id' => $this->submissionId,
            'contact' => [
                'organization_name' => $this->organizationName,
            ],
            'brief' => [
                'problem_statement' => $this->problemStatement,
                'desired_outcome' => $this->desiredOutcome,
                'constraints' => $this->constraints,
                'additional_context' => $this->additionalContext,
            ],
        ];
    }
}
