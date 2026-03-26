<?php

namespace App\Jobs;

use App\Models\Recommendation;
use App\Models\Plat;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AnalyzePlateWithAI implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        public User $user,
        public Plat $plat,
        public Recommendation $recommendation
    ) {}

    public function handle()
    {
        $ingredients = $this->plat->ingredients->pluck('tags')->flatten()->unique()->implode(', ');
        $restrictions = implode(', ', $this->user->dietary_tags ?? []);

        $prompt = "Analyze nutritional compatibility. 
        DISH: {$this->plat->nom}. 
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

                $result = $this->parseResponse($rawText);
                $this->recommendation->update([
                    'score'           => $result['score'],
                    'label'           => $result['label'],
                    'warning_message' => $result['warning_message'],
                    'status'          => 'ready' 
                ]);
                
                Log::info("Recommendation {$this->recommendation->id} updated to ready.");
            } else {
                Log::error("Groq API Error: " . $response->body());
            }
        } catch (\Exception $e) {
            Log::error("Job Failed: " . $e->getMessage());
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