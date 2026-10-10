<?php

namespace App\Http\Controllers;

use App\Enums\IntakeSubmissionStatus;
use App\Models\IntakeSubmission;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ReviewerSubmissionController extends Controller
{
    public function index(Request $request): View
    {
        $status = $request->string('status')->toString();
        $statusFilter = IntakeSubmissionStatus::tryFrom($status);

        $submissions = IntakeSubmission::query()
            ->with('clientContact')
            ->when(
                $statusFilter,
                fn ($query) => $query->where('status', $statusFilter->value),
            )
            ->latest('submitted_at')
            ->paginate(10)
            ->withQueryString();

        return view('reviewer.submissions.index', [
            'submissions' => $submissions,
            'selectedStatus' => $statusFilter,
            'statuses' => IntakeSubmissionStatus::cases(),
        ]);
    }

    public function show(IntakeSubmission $submission): View
    {
        $submission->load([
            'clientContact',
            'projectBrief',
            'statusHistories' => fn ($query) => $query->latest('created_at'),
        ]);

        return view('reviewer.submissions.show', [
            'submission' => $submission,
        ]);
    }
}
