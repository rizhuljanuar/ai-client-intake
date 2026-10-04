<?php

namespace App\Models;

use App\Enums\IntakeSubmissionStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class SubmissionStatusHistory extends Model
{
    use HasFactory;

    public const UPDATED_AT = null;

    protected $fillable = [
        'from_status',
        'to_status',
        'reason'
    ];

    protected function casts(): array
    {
        return [
            'from_status' => IntakeSubmissionStatus::class,
            'to_status' => IntakeSubmissionStatus::class,
            'created_at' => 'immutable_datetime',
        ];
    }

    public function submission(): BelongsTo
    {
        return $this->belongsTo(IntakeSubmission::class, 'intake_submission_id');
    }

    public function actor(): MorphTo
    {
        return $this->morphTo();
    }
}
