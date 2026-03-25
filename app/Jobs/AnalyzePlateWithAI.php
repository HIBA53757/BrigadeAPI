<?php 
namespace App\Jobs;

use App\Models\Recommendation;
use App\Models\Plat;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Http;

class AnalyzePlateWithAI implements ShouldQueue
{
    use Dispatchable, Queueable;

    public function __construct(
        public User $user,
        public Plat $plat,
        public Recommendation $recommendation
    ) {}

    public function handle()
    {
        $ingredients = $this->plat->ingredients->pluck('tags')->flatten()->unique()->implode(', ');
        $restrictions = implode(', ', $this->user->dietary_tags ?? []);

        $prompt = "Analyze the nutritional compatibility between this dish and the user's dietary restrictions.
        DISH: {$this->plat->nom}
        INGREDIENT TAGS: {$ingredients}
        USER RESTRICTIONS: {$restrictions}

        Tag mapping rules:
        'vegan' conflicts with: contains_meat, contains_lactose
        'no_sugar' conflicts with: contains_sugar
        'no_cholesterol' conflicts with: contains_cholesterol
        'gluten_free' conflicts with: contains_gluten
        'no_lactose' conflicts with: contains_lactose

        Calculate score: start at 100, subtract 25 for each conflict found.
        Respond ONLY with this JSON (no markdown, no explanation):
        {\"score\": <0-100>, \"warning_message\": \"<en français si score < 50, sinon chaine vide>\"}";

        $response = Http::withToken(config('services.groq.key'))
            ->post('https://api.groq.com/openai/v1/chat/completions', [
                'model' => 'llama3-8b-8192', 
                'messages' => [
                    ['role' => 'user', 'content' => $prompt]
                ],
                'temperature' => 0 
            ]);

        $result = json_decode($response->json()['choices'][0]['message']['content'], true);

        $label = 'Highly Recommended';
        if ($result['score'] < 80) $label = 'Recommended with notes';
        if ($result['score'] < 50) $label = 'Not Recommended';

        $this->recommendation->update([
            'score' => $result['score'],
            'label' => $label,
            'warning_message' => $result['warning_message'],
            'status' => 'ready'
        ]);
    }
}