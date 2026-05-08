<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Représente l'avis d'un apprenant sur une formation.
 */
class Rating extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'formation_id',
        'note',
        'commentaire',
    ];

    public const UPDATED_AT = null;

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'note' => 'integer',
            'created_at' => 'datetime',
        ];
    }

    /**
     * Retourne l'apprenant qui a posté l'avis.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Retourne la formation notée.
     */
    public function formation(): BelongsTo
    {
        return $this->belongsTo(Formation::class);
    }
}

