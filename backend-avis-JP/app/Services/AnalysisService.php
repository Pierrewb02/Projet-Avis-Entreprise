<?php

namespace App\Services;

class AnalysisService
{
    public function analyze(string $text): array
    {
        $text = mb_strtolower($text);
        
        // Listes enrichies pour être plus précis
        $positives = ['bon', 'super', 'excellent', 'rapide', 'parfait', 'top', 'satisfait', 'génial', 'merci', 'recommande', 'incroyable'];
        $negatives = ['mauvais', 'nul', 'lent', 'horrible', 'déçu', 'problème', 'cher', 'dommage', 'arnaque', 'attente', 'catastrophique'];

        $posCount = 0;
        $negCount = 0;

        foreach ($positives as $word) {
            if (str_contains($text, $word)) $posCount++;
        }

        foreach ($negatives as $word) {
            if (str_contains($text, $word)) $negCount++;
        }

        // Calcul du score (base 50)
        $scoreValue = 50 + ($posCount * 15) - ($negCount * 15);
        $scoreValue = max(0, min(100, $scoreValue));

        // Sentiment basé sur le score
        $sentiment = 'neutral';
        if ($scoreValue > 55) $sentiment = 'positive';
        if ($scoreValue < 45) $sentiment = 'negative';

        return [
            'sentiment' => $sentiment,
            'score'     => $scoreValue / 100, // <--- ICI : On transforme 85 en 0.85 pour ta colonne FLOAT
            'topics'    => $this->detectTopics($text)
        ];
    }

    private function detectTopics($text): array
    {
        $topics = [];
        $keywords = [
            'Livraison' => ['livraison', 'reçu', 'colis', 'envoi', 'reception'],
            'Prix'      => ['prix', 'cher', 'coûte', 'argent', 'abonnement', 'tarif'],
            'Qualité'   => ['qualité', 'service', 'vidéo', 'film', 'série', 'produit'],
            'Support'   => ['aide', 'support', 'contact', 'réponse', 'sav']
        ];

        foreach ($keywords as $topic => $words) {
            foreach ($words as $word) {
                if (str_contains($text, $word)) {
                    $topics[] = $topic;
                    break; 
                }
            }
        }
        
        return array_unique($topics);
    }
}