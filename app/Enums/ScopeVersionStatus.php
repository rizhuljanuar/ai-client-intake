<?php

namespace App\Enums;

enum ScopeVersionStatus: string
{
    case Draft = 'draft';
    case ReadyForApproval = 'ready_for_approval';
    case Approved = 'approved';
    case Rejected = 'rejected';
    case Superseded = 'superseded';
    case Withdrawn = 'withdrawn';
}
