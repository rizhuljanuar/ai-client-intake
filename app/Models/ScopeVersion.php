<?php

namespace App\Models;

use App\Enums\ScopeVersionSource;
use App\Enums\ScopeVersionStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class ScopeVersion extends Model
{
    use HasFactory;

    protected $fillable = [
        'intake_submission_id',
        'parent_version_id',
        'version_number',
        'status',
        'source_type',
        'source_draft_id',
        'snapshot',
        'change_summary',
    ];

    protected function casts(): array
    {
        return [
            'status' => ScopeVersionStatus::class,
            'source_type' => ScopeVersionSource::class,
            'snapshot' => 'array',
            'version_number' => 'integer',
        ];
    }

    public function submission(): BelongsTo
    {
        return $this->belongsTo(IntakeSubmission::class, 'intake_submission_id');
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_version_id');
    }

    public function sourceDraft(): BelongsTo
    {
        return $this->belongsTo(ScopeReviewDraft::class, 'source_draft_id');
    }

    public function createdBy(): MorphTo
    {
        return $this->morphTo();
    }

    public function approvalDecision(): HasOne
    {
        return $this->hasOne(ApprovalDecision::class);
    }
}
