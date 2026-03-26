<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Recommendation;
use App\Models\Plat;
use App\Jobs\AnalyzePlateWithAI;
use Illuminate\Http\Request;

class RecommendationController extends Controller
{
    public function analyze($plat_id)
    {
          $plat = Plat::findOrFail($plat_id);

        $rec = Recommendation::create([
            'user_id' => auth()->id(),
            'plat_id' => $plat->id,
            'status'  => 'processing'
        ]);

      
           $job = new AnalyzePlateWithAI(auth()->user(), $plat, $rec);
  $job->handle(); 
        $rec->refresh();

        return response()->json([
                  'message' => 'Analyse terminée par l\'IA',
            'recommendation_id' => $rec->id,
            'score' => $rec->score,
             'label' => $rec->label,
            'warning_message' => $rec->warning_message,
            'status' => $rec->status
        ], 200);
    }

        public function show($plate_id)
    {
        return Recommendation::where('user_id', auth()->id())
                           
    ->where('plat_id', $plate_id)
            ->latest()
                     ->firstOrFail();
    }
}