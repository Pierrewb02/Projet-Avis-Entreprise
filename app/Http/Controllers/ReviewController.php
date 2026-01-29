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

    public function store(Request $request) {
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

    public function index(Request $request)
    {
        // ON SUPPRIME 'comments.user' CAR TU N'AS PAS LE MODÈLE
        $query = Review::with(['user'])->latest();

        if ($request->user()->role !== 'admin') {
            $query->where('user_id', $request->user()->id);
        }

        return $query->get();
    }

    public function stats(Request $request)
{
    $user = $request->user();
    $query = Review::query();

    // 1. Filtre de sécurité (Admin voit tout, User voit les siens)
    if ($user->role !== 'admin') {
        $query->where('user_id', $user->id);
    }

    // 2. NOUVEAU : Filtre par entreprise (Sujet)
    // Si l'URL contient ?subject=NomDeLentreprise
    if ($request->has('subject') && $request->subject !== '') {
        $query->where('subject', 'LIKE', '%' . $request->subject . '%');
    }

    return response()->json([
        'total'     => (clone $query)->count(),
        'positive'  => (clone $query)->where('sentiment', 'positive')->count(),
        'negative'  => (clone $query)->where('sentiment', 'negative')->count(),
        'avg_score' => round((clone $query)->avg('sentiment_score') ?? 0, 1),
    ]);
}

    public function destroy(Request $request, $id)
    {
        $review = Review::findOrFail($id);
        if ($request->user()->role !== 'admin' && $review->user_id !== $request->user()->id) {
            return response()->json(['message' => 'Non autorisé'], 403);
        }
        $review->delete();
        return response()->json(['message' => 'Supprimé']);
    }
}