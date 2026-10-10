<?php

namespace App\AI\ScopeReview;

interface ScopeReviewGenerator
{
    public function generate(ScopeReviewInput $input): ScopeReviewDraft;
}
