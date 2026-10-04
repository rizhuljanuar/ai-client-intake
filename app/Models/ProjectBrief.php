<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProjectBrief extends Model
{
    use HasFactory;

    protected $fillable = [
        'problem_statement',
        'desired_outcome',
        'constrains',
        'additional_context',
    ];

    public function submission(): BelongsTo
    {
        return $this->belongsTo(IntakeSubmission::class, 'intake_submission_id');
    }
}
