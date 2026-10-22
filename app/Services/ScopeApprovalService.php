<?php

namespace App\Services;

use App\Enums\ApprovalDecisionType;
use App\Enums\ScopeVersionStatus;
use App\Models\ApprovalDecision;
use App\Models\ScopeVersion;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use LogicException;

class ScopeApprovalService
{
    public function approve(
        ScopeVersion $version,
        Model $approver,
        string $note,
    ): ApprovalDecision {
        return $this->decide(
            version: $version,
            approver: $approver,
            decision: ApprovalDecisionType::Approved,
            note: $note,
        );
    }

    public function reject(
        ScopeVersion $version,
        Model $approver,
        string $note,
    ): ApprovalDecision {
        return $this->decide(
            version: $version,
            approver: $approver,
            decision: ApprovalDecisionType::Rejected,
            note: $note,
        );
    }

    private function decide(
        ScopeVersion $version,
        Model $approver,
        ApprovalDecisionType $decision,
        string $note,
    ): ApprovalDecision {
        $note = trim($note);

        if ($version->status !== ScopeVersionStatus::ReadyForApproval) {
            throw new LogicException(
                'Hanya version ready_for_approval yang dapat diputuskan.'
            );
        }

        if ($note === '') {
            throw new LogicException('Catatan keputusan wajib diisi.');
        }

        return DB::transaction(function () use (
            $version,
            $approver,
            $decision,
            $note,
        ): ApprovalDecision {
            $lockedVersion = ScopeVersion::query()
                ->lockForUpdate()
                ->findOrFail($version->id);

            if ($lockedVersion->status !== ScopeVersionStatus::ReadyForApproval) {
                throw new LogicException('Version sudah diputuskan oleh proses lain.');
            }

            $approval = $lockedVersion->approvalDecision()->create([
                'decision' => $decision,
                'note' => $note,
                'decided_at' => now(),
            ]);

            $approval->decidedBy()->associate($approver);
            $approval->save();

            $lockedVersion->update([
                'status' => $decision === ApprovalDecisionType::Approved
                    ? ScopeVersionStatus::Approved
                    : ScopeVersionStatus::Rejected,
            ]);

            if ($decision === ApprovalDecisionType::Approved) {
                $lockedVersion->submission()->update([
                    'active_scope_version_id' => $lockedVersion->id,
                ]);
            }

            return $approval->refresh();
        });
    }
}
