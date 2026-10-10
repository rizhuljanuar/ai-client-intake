<?php

namespace App\Models;

use App\Enums\IntakeSubmissionStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class IntakeSubmission extends Model
{
    use HasFactory;

    protected $fillable = [
        'client_contact_id',
        'contact_name_snapshot',
        'contact_email_snapshot',
        'organization_name_snapshot',
        'status',
        'submitted_at',
        'last_processed_at',
    ];

    protected function casts(): array
    {
        return [
            'status' => IntakeSubmissionStatus::class,
            'submitted_at' => 'immutable_datetime',
            'last_processed_at' => 'immutable_datetime',
        ];
    }

    public function clientContact(): BelongsTo
    {
        return $this->belongsTo(ClientContact::class);
    }

    public function projectBrief(): HasOne
    {
        return $this->hasOne(ProjectBrief::class);
    }

    public function statusHistories(): HasMany
    {
        return $this->hasMany(SubmissionStatusHistory::class);
    }

    public function scopeReviewDraft(): HasOne
    {
        return $this->hasOne(ScopeReviewDraft::class);
    }
}
