<?php

namespace App\Enums;

enum IntakeSubmissionStatus: string
{
    case Submitted = 'submitted';
    case InReview = 'in_review';
    case NeedsClarification = 'needs_clarification';
    case ReadyForAiReview = 'ready_for_ai_review';
    case Processing = 'processing';
    case DraftReady = 'draft_ready';
    case Failed = 'failed';
    case Closed = 'closed';
}
