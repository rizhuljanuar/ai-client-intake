<?php

use App\Http\Controllers\HomeController;
use App\Http\Controllers\IntakeSubmissionController;
use App\Http\Controllers\ReviewerReviewWorkspaceController;
use App\Http\Controllers\ReviewerScopeReviewController;
use App\Http\Controllers\ReviewerSubmissionController;
use App\Http\Controllers\ScopeDecisionController;
use App\Models\IntakeSubmission;
use App\Models\ScopeVersion;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->name('home');

Route::view('/login', 'auth.login')->name('login');

Route::get('/intake', [IntakeSubmissionController::class, 'create'])->name('intake.create');
Route::post('/intake', [IntakeSubmissionController::class, 'store'])->name('intake.store');
Route::get('/intake/submitted/{submission}', [IntakeSubmissionController::class, 'submitted'])->name('intake.submitted');

Route::prefix('reviewer')
    ->name('reviewer.')
    ->group(function () {
        Route::get('/submissions', [ReviewerSubmissionController::class, 'index'])
            ->name('submissions.index');

        Route::get('/submissions/{submission}', [ReviewerSubmissionController::class, 'show'])
            ->name('submissions.show');

        Route::get('/submissions/{submission}/draft', [ReviewerScopeReviewController::class, 'show'])
            ->name('submissions.draft.show');

        Route::get('/submissions/{submission}/review', [ReviewerReviewWorkspaceController::class, 'show'])
            ->name('submissions.review.show');
        //
        // Route::post('/versions/{version}/approve', [ScopeDecisionController::class, 'approve'])
        //     ->name('versions.approve');
        //
        // Route::post('/versions/{version}/reject', [ScopeDecisionController::class, 'reject'])
        //     ->name('versions.reject');
    });

Route::middleware('auth')
    ->prefix('reviewer/submissions/{submission}')
    ->name('reviewer.submissions.')
    ->scopeBindings()
    ->group(function () {
        Route::get('/versions/{version}', function (
            IntakeSubmission $submission,
            ScopeVersion $version,
        ) {
            return view('reviewer.submissions.review', compact('submission', 'version'));
        })
            ->middleware('can:view,version')
            ->name('versions.show');

        Route::post('/versions/{version}/approve', [ScopeDecisionController::class, 'approve'])
            ->middleware('can:decide,version')
            ->name('versions.approve');

        Route::post('/versions/{version}/reject', [ScopeDecisionController::class, 'reject'])
            ->middleware('can:decide,version')
            ->name('versions.reject');
    });
