<?php

use App\Http\Controllers\HomeController;
use App\Http\Controllers\IntakeSubmissionController;
use App\Http\Controllers\ReviewerReviewWorkspaceController;
use App\Http\Controllers\ReviewerScopeReviewController;
use App\Http\Controllers\ReviewerSubmissionController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->name('home');

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
    });

Route::prefix('reviewer')
    ->name('reviewer.')
    ->group(function () {
        Route::get('/submissions/{submission}/draft', [ReviewerScopeReviewController::class, 'show'])
            ->name('submissions.draft.show');
    });

Route::prefix('reviewer')
    ->name('reviewer.')
    ->group(function () {
        Route::get('/submissions/{submission}/review', [ReviewerReviewWorkspaceController::class, 'show'])
            ->name('submissions.review.show');
    });
