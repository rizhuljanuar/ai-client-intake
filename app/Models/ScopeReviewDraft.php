<?php

namespace App\Models;

use App\Enums\ScopeReviewDraftStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ScopeReviewDraft extends Model
{
    use HasFactory;

    protected $fillable = [
        'intake_submission_id',
        'contract_version',
        'status',
        'draft_payload',
        'provider_mode',
        'prompt_revision',
        'error_code',
        'error_message',
        'started_at',
        'completed_at',
    ];

    protected function casts(): array
    {
        return [
            'status' => ScopeReviewDraftStatus::class,
            'draft_payload' => 'array',
            'started_at' => 'immutable_datetime',
            'completed_at' => 'immutable_datetime',
        ];
    }

    public function submission(): BelongsTo
    {
        return $this->belongsTo(IntakeSubmission::class, 'intake_submission_id');
    }
}
