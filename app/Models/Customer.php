<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Customer extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'customers';

    protected $fillable = [
        'added_by',
        'updated_by',
        'name',
        'surname',
        'photo_url'
    ];

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class,'added_by');
    }

    public function updator(): BelongsTo
    {
        return $this->belongsTo(User::class,'updated_by');
    }
}
