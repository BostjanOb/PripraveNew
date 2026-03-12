<?php

namespace App\Models;

use Database\Factories\ReportReasonFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ReportReason extends Model
{
    /** @use HasFactory<ReportReasonFactory> */
    use HasFactory;

    public function reports(): HasMany
    {
        return $this->hasMany(Report::class);
    }
}
