<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class FileImport extends Model
{
    use HasFactory;

    protected $fillable = [
        'file_id',
        'file_path',
        'started_at',
        'completed_at',
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    public function records(): HasMany
    {
        return $this->hasMany(Record::class);
    }
}
