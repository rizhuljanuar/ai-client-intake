<?php

namespace App\Http\Controllers;

use App\Models\IntakeSubmission;
use Illuminate\View\View;

class ReviewerReviewWorkspaceController extends Controller
{
    public function show(IntakeSubmission $submission): View
    {
        $submission->load([
            'clientContact',
            'projectBrief',
            'scopeReviewDraft',
            'statusHistories',
        ]);

        return view('reviewer.workspace.show', [
            'submission' => $submission,
            'activeRevision' => null,
            'candidates' => collect(),
            'questions' => collect(),
            'risks' => collect(),
            'assumptions' => collect(),
        ]);
    }
}
