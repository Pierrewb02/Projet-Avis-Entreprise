<?php

namespace App\Http\Controllers;

use App\Models\Review;
use App\Services\AnalysisService;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    protected $ai;

    public function __construct(AnalysisService $ai) {
        $this->ai = $ai;
    }

    /**
     * POSTER UN AVIS
     */
    public function store(Request $request) {
        // SÉCURITÉ : Seul le rôle 'user' (le particulier) peut créer un avis
        if ($request->user()->role !== 'user') {
            return response()->json([
                'message' => 'Action interdite : Seuls les clients peuvent publier des avis.'
            ], 403);
        }

        $request->validate([
            'subject' => 'required|string',
            'comment' => 'required|string',
            'stars'   => 'required|integer|min:1|max:5'
        ]);

        $analysis = $this->ai->analyze($request->comment);

        $review = Review::create([
            'user_id'         => $request->user()->id,
            'subject'         => $request->subject,
            'comment'         => $request->comment,
            'stars'           => $request->stars,
            'sentiment'       => $analysis['sentiment'],
            'sentiment_score' => $analysis['score'],
            'topics'          => $analysis['topics'] ?? [], 
        ]);

        return response()->json($review->load('user'), 201);
    }

    /**
     * LISTE DES AVIS (Filtrée par rôle)
     */
    public function index(Request $request)
    {
        $user = $request->user();
        $query = Review::with(['user', 'comments.user']);

        if ($user->role === 'entreprise') {
            // L'entreprise ne voit QUE les avis qui la concernent
            $query->where('subject', $user->company_name);
        } elseif ($user->role === 'user') {
            // Le client ne voit que SES propres avis
            $query->where('user_id', $user->id);
        }
        // L'admin voit tout par défaut

        return $query->latest()->get();
    }

    /**
     * STATISTIQUES (Filtrées par rôle)
     */
    public function stats(Request $request)
    {
        $user = $request->user();
        $query = Review::query();

        // 1. Filtrage de base selon le rôle
        if ($user->role === 'entreprise') {
            // Stats uniquement sur son entreprise
            $query->where('subject', $user->company_name);
        } elseif ($user->role === 'user') {
            // Stats uniquement sur ses propres publications
            $query->where('user_id', $user->id);
        } 
        // L'admin peut filtrer via le paramètre ?subject=... (ce que fait ta Home.vue)
        elseif ($user->role === 'admin' && $request->has('subject') && $request->subject !== '') {
            $query->where('subject', 'LIKE', '%' . $request->subject . '%');
        }

        return response()->json([
            'total'     => (clone $query)->count(),
            'positive'  => (clone $query)->where('sentiment', 'positive')->count(),
            'negative'  => (clone $query)->where('sentiment', 'negative')->count(),
            'avg_score' => round((clone $query)->avg('sentiment_score') ?? 0, 1),
        ]);
    }

    /**
     * SUPPRESSION
     */
    public function destroy(Request $request, $id)
    {
        $review = Review::findOrFail($id);
        
        // Sécurité : Seul l'admin ou l'auteur de l'avis peut supprimer
        if ($request->user()->role !== 'admin' && $review->user_id !== $request->user()->id) {
            return response()->json(['message' => 'Non autorisé'], 403);
        }
        
        $review->delete();
        return response()->json(['message' => 'Supprimé']);
    }

    public function getStats(Request $request)
{
    if ($request->has('subject')) {
    $query->where('subject', 'LIKE', '%' . $request->subject . '%');
    }
    $user = $request->user();
    $query = \App\Models\Review::query();

    // FILTRE : Si c'est une entreprise, elle ne voit que SES stats
    if ($user->role === 'entreprise') {
        $query->where('subject', $user->company_name);
    } 
    // FILTRE : Si c'est un utilisateur, il voit SES stats personnelles
    elseif ($user->role === 'user') {
        $query->where('user_id', $user->id);
    }
    // Si c'est Admin, il voit TOUT (pas de filtre)

    $total = $query->count();
    $positive = (clone $query)->where('sentiment', 'positive')->count();
    $negative = (clone $query)->where('sentiment', 'negative')->count();
    
    // Calcul de la moyenne du score (ex: 0.75)
    $avgScore = $query->avg('sentiment_score') ?: 0;

    return response()->json([
        'total' => $total,
        'positive' => $positive,
        'negative' => $negative,
        'avg_score' => $avgScore
    ]);
}

public function update(Request $request, $id)
{
    $review = \App\Models\Review::findOrFail($id);

    // Sécurité : Seul l'auteur de l'avis peut le modifier
    if ($request->user()->id !== $review->user_id) {
        return response()->json(['message' => 'Non autorisé'], 403);
    }

    $request->validate([
        'subject' => 'required|string',
        'comment' => 'required|string',
        'stars' => 'required|integer|min:1|max:5',
    ]);

    // On met à jour les champs
    $review->subject = $request->subject;
    $review->comment = $request->comment;
    $review->stars = $request->stars;

    // IMPORTANT : On relance l'analyse de l'IA car le commentaire a changé !
    // (En supposant que ton service d'analyse s'appelle AnalysisService)
    $analysis = app(\App\Services\AnalysisService::class)->analyze($request->comment);
    $review->sentiment = $analysis['sentiment'];
    $review->sentiment_score = $analysis['score'];

    $review->save();

    return response()->json([
        'message' => 'Avis mis à jour avec succès',
        'review' => $review
    ]);
}
}