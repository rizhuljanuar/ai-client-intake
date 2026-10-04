<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ClientContact extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'email',
        'organization_name',
    ];

    public function submissions(): HasMany
    {
        return $this->hasMany(IntakeSubmission::class);
    }
}
