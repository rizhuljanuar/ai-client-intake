<?php

namespace App\Enums;

enum ScopeVersionSource: string
{
    case Ai = 'ai';
    case Reviewer = 'reviewer';
    case Merge = 'merge';
}
