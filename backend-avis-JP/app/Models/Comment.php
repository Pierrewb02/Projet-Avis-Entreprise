<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    use HasFactory;

    protected $fillable = [
        'review_id',
        'user_id',
        'content'
    ];

    /**
     * Récupérer l'utilisateur qui a écrit le commentaire (L'Admin)
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Récupérer l'avis auquel ce commentaire appartient
     */
    public function review()
    {
        return $this->belongsTo(Review::class);
    }
}
