<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AIService
{
    public function analyzeCompatibility($user, $plat)
    {
        $ingredients = $plat->ingredients->pluck('tags')->flatten()->unique()->implode(', ');
        $restrictions = implode(', ', $user->dietary_tags ?? []);

        $prompt = "Analyze nutritional compatibility. 
        DISH: {$plat->nom}. 
        INGREDIENTS: {$ingredients}. 
        USER RESTRICTIONS: {$restrictions}.
        Respond ONLY with JSON: {\"score\": 0-100, \"warning_message\": \"message en français\"}";

        try {
            $response = Http::withToken(config('services.groq.key'))
                ->post('https://api.groq.com/openai/v1/chat/completions', [
                    'model' => 'llama-3.1-8b-instant',
                    'messages' => [['role' => 'user', 'content' => $prompt]],
                    'temperature' => 0
                ]);

            if ($response->successful()) {
                $rawText = $response->json()['choices'][0]['message']['content'] ?? '';
                Log::info("IA RAW RESPONSE: " . $rawText);
                return $this->parseResponse($rawText);
            }

            Log::error("Groq API Error: " . $response->body());
            return null;

        } catch (\Exception $e) {
            Log::error("Service AI Failed: " . $e->getMessage());
            return null;
        }
    }

    private function parseResponse(string $text): array
    {
        $text = preg_replace('/```json|```/', '', $text);
        $text = trim($text);
        preg_match('/{.*}/s', $text, $matches);
        $data = json_decode($matches[0] ?? '{}', true);

        $score = isset($data['score']) ? max(0, min(100, (int) $data['score'])) : 50;
        
        $label = match(true) {
            $score >= 80 => 'Highly Recommended',
            $score >= 50 => 'Recommended with notes',
            default      => 'Not Recommended',
        };

        return [
            'score'           => $score,
            'label'           => $label,
            'warning_message' => $data['warning_message'] ?? 'Analyse terminée.',
        ];
    }
}