<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Recommendation;
use App\Models\Plat;
use App\Jobs\AnalyzePlateWithAI;
use Illuminate\Http\Request;

class RecommendationController extends Controller
{
    public function analyze(Plat $plat)
    {
        $rec = Recommendation::create([
            'user_id' => auth()->id(),
            'plat_id' => $plat->id,
            'status' => 'processing'
        ]);

        AnalyzePlateWithAI::dispatch(auth()->user(), $plat, $rec);

        return response()->json([
            'message' => 'L\'analyse IA (Llama) a commencé.',
            'recommendation_id' => $rec->id,
            'status' => 'processing'
        ], 202);
    }

    public function index()
    {
        return Recommendation::where('user_id', auth()->id())->with('plat')->get();
    }

    public function show($plate_id)
    {
        return Recommendation::where('user_id', auth()->id())
                             ->where('plat_id', $plate_id)
                             ->latest()
                             ->firstOrFail();
    }
}