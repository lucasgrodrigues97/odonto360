<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Specialization extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'description',
        'is_active',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Get the dentists that have this specialization.
     */
    public function dentists()
    {
        return $this->belongsToMany(Dentist::class, 'dentist_specializations');
    }

    /**
     * Scope a query to only include active specializations.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
