<?php

namespace App\AI\ScopeReview;

use RuntimeException;

final class ScopeReviewValidationException extends RuntimeException
{
    public function __construct(
        string $message,
        public readonly array $errors,
    ) {
        parent::__construct($message);
    }
}
