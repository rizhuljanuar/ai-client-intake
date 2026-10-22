<?php

namespace App\Models;

use App\Enums\ApprovalDecisionType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class ApprovalDecision extends Model
{
    use HasFactory;

    protected $fillable = [
        'scope_version_id',
        'decision',
        'note',
        'decided_at',
    ];

    protected function casts(): array
    {
        return [
            'decision' => ApprovalDecisionType::class,
            'decided_at' => 'immutable_datetime',
        ];
    }

    public function version(): BelongsTo
    {
        return $this->belongsTo(ScopeVersion::class, 'scope_version_id');
    }

    public function decidedBy(): MorphTo
    {
        return $this->morphTo();
    }
}
