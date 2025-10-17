<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class UsersDrug extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'users_drugs';

    protected $fillable = [
        'user_id',
        'rxcui',
    ];

    public static function getTableName(): string
    {
        return (new static())->getTable();
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function drug(): BelongsTo
    {
        return $this->belongsTo(Drug::class, 'rxcui', 'rxcui');
    }
}
