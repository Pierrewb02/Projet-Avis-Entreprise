<?php

namespace App\Services;

class AnalysisService
{
    public function analyze(string $text): array
    {
        $text = mb_strtolower($text);
        
        // Liste de mots-clés (Option 1 du projet) [cite: 28, 30]
        $positives = ['bon', 'super', 'excellent', 'rapide', 'parfait', 'top', 'satisfait'];
        $negatives = ['mauvais', 'nul', 'lent', 'horrible', 'déçu', 'problème', 'cher'];

        $sentiment = 'neutral';
        $score = 50; // Score par défaut (0-100) [cite: 34]

        foreach ($positives as $word) {
            if (str_contains($text, $word)) {
                $sentiment = 'positive';
                $score = 85;
            }
        }

        foreach ($negatives as $word) {
            if (str_contains($text, $word)) {
                $sentiment = 'negative';
                $score = 20;
            }
        }

        return [
            'sentiment' => $sentiment,
            'score' => $score,
            'topics' => $this->detectTopics($text) // Option 2 [cite: 32]
        ];
    }

    private function detectTopics($text): array
    {
        $topics = [];
        if (str_contains($text, 'livraison')) $topics[] = 'Livraison';
        if (str_contains($text, 'prix') || str_contains($text, 'cher')) $topics[] = 'Prix';
        if (str_contains($text, 'qualité')) $topics[] = 'Qualité';
        
        return $topics;
    }
}