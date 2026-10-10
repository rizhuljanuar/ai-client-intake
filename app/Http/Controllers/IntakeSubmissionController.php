<?php

namespace App\Http\Controllers;

use App\Enums\IntakeSubmissionStatus;
use App\Http\Requests\StoreIntakeSubmissionRequest;
use App\Models\ClientContact;
use App\Models\IntakeSubmission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class IntakeSubmissionController extends Controller
{
    public function create(): View
    {
        return view('intake.create');
    }

    public function store(StoreIntakeSubmissionRequest $request)
    {
        $submission = DB::transaction(function () use ($request): IntakeSubmission {
            $validated = $request->validated();

            $contact = ClientContact::create([
                'name' => $validated['contact_name'],
                'email' => $validated['contact_email'],
                'organization_name' => $validated['organization_name'] ?? null,
            ]);

            $submission = IntakeSubmission::create([
                'client_contact_id' => $contact->id,
                'contact_name_snapshot' => $contact->name,
                'contact_email_snapshot' => $contact->email,
                'organization_name_snapshot' => $contact->organization_name,
                'status' => IntakeSubmissionStatus::Submitted,
                'submitted_at' => now(),
            ]);

            $submission->projectBrief()->create([
                'problem_statement' => $validated['problem_statement'],
                'desired_outcome' => $validated['desired_outcome'],
                'constraints' => $validated['constraints'] ?? null,
                'additional_context' => $validated['additional_context'] ?? null,
            ]);

            $submission->statusHistories()->create([
                'form_status' => null,
                'to_status' => IntakeSubmissionStatus::Submitted,
                'reason' => 'Form intake diterima',
            ]);

            return $submission;
        });

        return to_route('intake.submitted', $submission);
    }

    public function submitted(IntakeSubmission $submission): View
    {
        $submission->load(['clientContact', 'projectBrief']);

        return view('intake.submitted', [
            'submission' => $submission
        ]);
    }
}
