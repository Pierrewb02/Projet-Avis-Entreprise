<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\Review; // Import important !
use Illuminate\Http\Request;

class CommentController extends Controller
{
    /**
     * Enregistre une réponse officielle (Réservé à l'entreprise concernée)
     */
    public function store(Request $request, $reviewId)
    {
        $user = $request->user();
        $review = Review::findOrFail($reviewId);

        // 1. SÉCURITÉ : L'Admin n'a pas à répondre aux clients (rôle de modérateur uniquement)
        if ($user->role === 'admin') {
            return response()->json(['message' => 'L\'administrateur ne peut pas poster de réponses officielles.'], 403);
        }

        // 2. SÉCURITÉ : L'entreprise peut répondre SEULEMENT si l'avis la concerne
        if ($user->role === 'entreprise') {
            if ($review->subject !== $user->company_name) {
                return response()->json(['message' => 'Vous ne pouvez répondre qu\'aux avis concernant votre entreprise.'], 403);
            }
        } else {
            // Un utilisateur classique ('user') ne peut pas répondre aux avis des autres
            return response()->json(['message' => 'Action non autorisée.'], 403);
        }

        // 3. Validation du contenu
        $request->validate([
            'content' => 'required|string|max:1000'
        ]);

        // 4. Création en base de données
        $comment = Comment::create([
            'review_id' => $reviewId,
            'user_id'   => $user->id,
            'content'   => $request->content,
        ]);

        return response()->json($comment->load('user'), 201);
    }
}