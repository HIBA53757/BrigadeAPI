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

    AnalyzePlateWithAI::dispatch(auth()->user(), $plat, $rec);

    return response()->json([
        'message' => 'Analyse lancée en arrière-plan',
        'recommendation_id' => $rec->id,
        'status' => 'processing'
    ], 202); 
}
public function show($recommendation_id) 
{
    return Recommendation::where('user_id', auth()->id())
                         ->findOrFail($recommendation_id);
}
}