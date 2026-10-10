<?php

namespace App\AI\ScopeReview;

final readonly class ScopeReviewDraft
{
    public function __construct(
        public string $contractVersion,
        public int $sourceSubmissionId,
        public string $summary,
        public array $unclearItems,
        public array $risks,
        public array $assumptions,
        public array $followUpQuestions,
        public array $reviewNotes,
        public array $generation,
    ) {
    }

    public function toArray(): array
    {
        return [
            'contract_version' => $this->contractVersion,
            'source_submission_id' => $this->sourceSubmissionId,
            'summary' => $this->summary,
            'unclear_items' => $this->unclearItems,
            'risks' => $this->risks,
            'assumptions' => $this->assumptions,
            'follow_up_questions' => $this->followUpQuestions,
            'review_notes' => $this->reviewNotes,
            'generation' => $this->generation,
        ];
    }
}
