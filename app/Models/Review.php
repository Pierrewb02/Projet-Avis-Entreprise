<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    
    // Champs que l'on autorise à remplir via l'API [cite: 64, 65, 66, 67]
    protected $fillable = [
    'user_id',
    'subject',
    'comment',
    'stars',
    'sentiment',
    'sentiment_score'
];

    // Conversion automatique du JSON (base de données) en Tableau (PHP/VueJS) [cite: 67]
    protected $casts = [
        'topics' => 'array',
        'comments' => 'array',
    ];

    public function user() {
    return $this->belongsTo(User::class);
    }

    public function comments() {
    return $this->hasMany(Comment::class);
    }

    
}
