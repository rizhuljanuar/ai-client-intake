<?php

namespace App\AI\ScopeReview;

final class ScopeReviewOutputValidator
{
    public function validate(array $payload, int $expectedSubmissionId): ScopeReviewDraft
    {
        $errors = [];

        if (($payload['contract_version'] ?? null) !== 'scope_review.v1') {
            $errors['contract_version'][] = 'Kontrak output tidak dikenal.';
        }

        if (($payload['source_submission_id'] ?? null) !== $expectedSubmissionId) {
            $errors['source_submission_id'][] = 'ID submission tidak sesuai konteks job.';
        }

        if (!is_string($payload['summary'] ?? null) || trim($payload['summary']) === '') {
            $errors['summary'][] = 'Summary wajib berupa string yang tidak kosong.';
        }

        foreach ([
            'unclear_items',
            'risks',
            'assumptions',
            'follow_up_questions',
            'review_notes',
            'generation',
        ] as $field) {
            if (!is_array($payload[$field] ?? null)) {
                $errors[$field][] = 'Field wajib berupa array atau object array.';
            }
        }

        $this->validateItems($payload['unclear_items'] ?? null, [
            'id', 'statement', 'source_fields', 'impact', 'question',
        ], 'unclear_items', $errors);

        $this->validateItems($payload['risks'] ?? null, [
            'id', 'title', 'description', 'severity', 'source_fields', 'mitigation',
        ], 'risks', $errors);

        $this->validateItems($payload['assumptions'] ?? null, [
            'id', 'statement', 'source_fields', 'needs_confirmation',
        ], 'assumptions', $errors);

        $this->validateItems($payload['follow_up_questions'] ?? null, [
            'id', 'question', 'related_item_ids', 'priority',
        ], 'follow_up_questions', $errors);

        $this->validateGeneration($payload['generation'] ?? null, $errors);
        $this->validateUniqueIds($payload, $errors);

        if ($errors !== []) {
            throw new ScopeReviewValidationException(
                'Output scope review tidak lolos validasi.',
                $errors,
            );
        }

        return new ScopeReviewDraft(
            contractVersion: $payload['contract_version'],
            sourceSubmissionId: $payload['source_submission_id'],
            summary: trim($payload['summary']),
            unclearItems: $payload['unclear_items'],
            risks: $payload['risks'],
            assumptions: $payload['assumptions'],
            followUpQuestions: $payload['follow_up_questions'],
            reviewNotes: $payload['review_notes'],
            generation: $payload['generation'],
        );
    }

    private function validateItems(
        mixed $items,
        array $requiredKeys,
        string $field,
        array &$errors,
    ): void {
        if (!is_array($items)) {
            return;
        }

        foreach ($items as $index => $item) {
            if (!is_array($item)) {
                $errors["{$field}.{$index}"][] = 'Item harus berupa object.';
                continue;
            }

            foreach ($requiredKeys as $key) {
                if (!array_key_exists($key, $item)) {
                    $errors["{$field}.{$index}.{$key}"][] = 'Key wajib tidak ada.';
                }
            }
        }

        if ($field === 'risks') {
            foreach ($items as $index => $item) {
                if (!is_array($item)) {
                    continue;
                }

                if (!in_array($item['severity'] ?? null, ['low', 'medium', 'high'], true)) {
                    $errors["{$field}.{$index}.severity"][] = 'Severity tidak dikenal.';
                }
            }
        }

        if ($field === 'follow_up_questions') {
            foreach ($items as $index => $item) {
                if (!is_array($item)) {
                    continue;
                }

                if (!in_array($item['priority'] ?? null, ['low', 'medium', 'high'], true)) {
                    $errors["{$field}.{$index}.priority"][] = 'Priority tidak dikenal.';
                }
            }
        }
    }

    private function validateGeneration(mixed $generation, array &$errors): void
    {
        if (!is_array($generation)) {
            return;
        }

        foreach (['provider_mode', 'generated_at', 'prompt_revision'] as $key) {
            if (!array_key_exists($key, $generation)) {
                $errors["generation.{$key}"][] = 'Metadata generation wajib ada.';
            }
        }

        if (!in_array($generation['provider_mode'] ?? null, ['fake', 'http'], true)) {
            $errors['generation.provider_mode'][] = 'Mode provider tidak dikenal.';
        }
    }

    private function validateUniqueIds(array $payload, array &$errors): void
    {
        foreach ([
            'unclear_items',
            'risks',
            'assumptions',
            'follow_up_questions',
        ] as $field) {
            if (!is_array($payload[$field] ?? null)) {
                continue;
            }

            $ids = [];

            foreach ($payload[$field] as $index => $item) {
                if (!is_array($item) || !isset($item['id'])) {
                    continue;
                }

                if (in_array($item['id'], $ids, true)) {
                    $errors["{$field}.{$index}.id"][] = 'ID item duplikat.';
                }

                $ids[] = $item['id'];
            }
        }
    }
}
