<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Record extends Model
{
    use HasFactory;
    protected $fillable = [
        'file_import_id',
        'record_type',
        'line_number',
        'data',
    ];

    protected $casts = [
        'data' => 'array',
    ];

    public function fileImport(): BelongsTo
    {
        return $this->belongsTo(FileImport::class);
    }
}
