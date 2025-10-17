<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Drug extends Model
{
    use HasFactory;

    protected $table = 'drugs';

    protected $fillable = [
        'rxcui',
        'name',
        'base_names',
        'dose_form_group_names',
    ];

    protected $casts = [
        'base_names'            => 'array',
        'dose_form_group_names' => 'array',
    ];

    public static function getTableName(): string
    {
        return (new static())->getTable();
    }

    /**
     * Get users who have this drug
     */
    public function usersDrugs(): HasMany
    {
        return $this->hasMany(UsersDrug::class, 'rxcui', 'rxcui');
    }
}
