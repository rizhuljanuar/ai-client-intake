<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreScopeDecisionRequest;
use App\Models\IntakeSubmission;
use App\Models\ScopeVersion;
use App\Services\ScopeApprovalService;
use Illuminate\Http\RedirectResponse;

class ScopeDecisionController extends Controller
{
    public function approve(
        StoreScopeDecisionRequest $request,
        IntakeSubmission $submission,
        ScopeVersion $version,
        ScopeApprovalService $service,
    ): RedirectResponse {
        $service->approve(
            version: $version,
            approver: $request->user(),
            note: $request->string('note')->toString(),
        );

        return $this->redirectToWorkspace($submission, $version);
    }

    public function reject(
        StoreScopeDecisionRequest $request,
        IntakeSubmission $submission,
        ScopeVersion $version,
        ScopeApprovalService $service,
    ): RedirectResponse {
        $service->reject(
            version: $version,
            approver: $request->user(),
            note: $request->string('note')->toString(),
        );

        return $this->redirectToWorkspace($submission, $version);
    }

    private function redirectToWorkspace(
        IntakeSubmission $submission,
        ScopeVersion $version,
    ): RedirectResponse {
        return to_route('reviewer.submissions.review.show', [
            'submission' => $submission,
            'version' => $version,
        ]);
    }
}
