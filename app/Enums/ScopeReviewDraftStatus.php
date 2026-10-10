<?php

namespace App\Enums;

enum ScopeReviewDraftStatus: string
{
    case Processing = 'processing';
    case Generated = 'generated';
    case FailedValidation = 'failed_validation';
    case Failed = 'failed';
}
