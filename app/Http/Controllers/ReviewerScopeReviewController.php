<?php

namespace App\Http\Controllers;

use App\Models\IntakeSubmission;
use Illuminate\View\View;

class ReviewerScopeReviewController extends Controller
{
    public function show(IntakeSubmission $submission): View
    {
        $submission->load([
            'clientContact',
            'projectBrief',
            'scopeReviewDraft',
        ]);

        return view('reviewer.submissions.draft', [
            'submission' => $submission,
            'draft' => $submission->scopeReviewDraft,
        ]);
    }
}
