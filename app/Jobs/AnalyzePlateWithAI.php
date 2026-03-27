<?php

namespace App\Jobs;

use App\Models\Recommendation;
use App\Models\Plat;
use App\Models\User;
use App\Services\AIService; // Importation du service
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class AnalyzePlateWithAI implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        public User $user,
        public Plat $plat,
        public Recommendation $recommendation
    ) {}

    public function handle(AIService $aiService)
    {
       
        $result = $aiService->analyzeCompatibility($this->user, $this->plat);

        if ($result) {
            $this->recommendation->update([
                'score'           => $result['score'],
                'label'           => $result['label'],
                'warning_message' => $result['warning_message'],
                'status'          => 'ready'
            ]);
            
            Log::info("Recommendation {$this->recommendation->id} updated via AIService.");
        } else {
            Log::error("Job failed because AIService returned no results.");
        }
    }
}