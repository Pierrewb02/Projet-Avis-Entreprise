<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    /**
     * Enregistre un nouveau commentaire (Réponse de l'admin)
     */
    public function store(Request $request, $reviewId)
    {
        // 1. Vérification de sécurité : SEUL l'admin peut commenter
        if ($request->user()->role !== 'admin') {
            return response()->json(['message' => 'Seul l\'administrateur peut répondre aux avis'], 403);
        }

        // 2. Validation du contenu
        $request->validate([
            'content' => 'required|string|max:1000'
        ]);

        // 3. Création en base de données
        $comment = Comment::create([
            'review_id' => $reviewId,
            'user_id'   => $request->user()->id,
            'content'   => $request->content,
        ]);

        // 4. On renvoie le commentaire avec les infos de l'utilisateur (le nom de l'admin)
        return response()->json($comment->load('user'), 201);
    }
}