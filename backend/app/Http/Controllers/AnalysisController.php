<?php
namespace App\Http\Controllers;
use App\Models\UserPortrait;
use App\Models\Tag;
use App\Models\TagCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class AnalysisController extends Controller {
    public function index(Request $request) {
        $userId = $request->user()->id;
        $totalPortraits = UserPortrait::where('user_id', $userId)->count();
        $activePortraits = UserPortrait::where('user_id', $userId)->where('status', 'active')->count();
        $genderDist = UserPortrait::where('user_id', $userId)
            ->whereNotNull('gender')
            ->select('gender', DB::raw('count(*) as count'))
            ->groupBy('gender')
            ->get();
        $ageDist = UserPortrait::where('user_id', $userId)
            ->whereNotNull('age')
            ->select(
                DB::raw("CASE
                    WHEN age < 18 THEN 'Under 18'
                    WHEN age BETWEEN 18 AND 24 THEN '18-24'
                    WHEN age BETWEEN 25 AND 34 THEN '25-34'
                    WHEN age BETWEEN 35 AND 44 THEN '35-44'
                    WHEN age BETWEEN 45 AND 54 THEN '45-54'
                    ELSE '55+' END as age_group"),
                DB::raw('count(*) as count')
            )
            ->groupBy('age_group')
            ->get();
        $locationDist = UserPortrait::where('user_id', $userId)
            ->whereNotNull('location')
            ->select('location', DB::raw('count(*) as count'))
            ->groupBy('location')
            ->orderByDesc('count')
            ->limit(10)
            ->get();
        $topTags = DB::table('portrait_tags')
            ->join('tags', 'portrait_tags.tag_id', '=', 'tags.id')
            ->join('user_portraits', 'portrait_tags.user_portrait_id', '=', 'user_portraits.id')
            ->where('user_portraits.user_id', $userId)
            ->select('tags.name', DB::raw('count(*) as count'))
            ->groupBy('tags.id', 'tags.name')
            ->orderByDesc('count')
            ->limit(10)
            ->get();
        $totalTags = Tag::where('user_id', $userId)->count();
        $totalCategories = TagCategory::where('user_id', $userId)->count();
        return response()->json([
            'total_portraits' => $totalPortraits,
            'active_portraits' => $activePortraits,
            'inactive_portraits' => $totalPortraits - $activePortraits,
            'total_tags' => $totalTags,
            'total_categories' => $totalCategories,
            'gender_distribution' => $genderDist,
            'age_distribution' => $ageDist,
            'location_distribution' => $locationDist,
            'top_tags' => $topTags,
        ]);
    }

    public function filter(Request $request) {
        $data = $request->validate([
            'tag_ids' => 'required|array|min:1',
            'tag_ids.*' => 'integer|exists:tags,id',
        ]);

        $tagIds = $data['tag_ids'];
        sort($tagIds);
        $userId = $request->user()->id;
        $cacheKey = "analysis:filter:{$userId}:" . implode(',', $tagIds);

        return Cache::remember($cacheKey, 300, function () use ($userId, $tagIds) {
            $baseQuery = UserPortrait::where('user_id', $userId)
                ->whereHas('tags', function ($q) use ($tagIds) {
                    $q->whereIn('tags.id', $tagIds);
                }, '=', count($tagIds));

            $matchedCount = (clone $baseQuery)->count();

            $genderDist = (clone $baseQuery)
                ->whereNotNull('gender')
                ->select('gender', DB::raw('count(*) as count'))
                ->groupBy('gender')
                ->get();

            $ageDist = (clone $baseQuery)
                ->whereNotNull('age')
                ->select(
                    DB::raw("CASE
                        WHEN age < 18 THEN 'Under 18'
                        WHEN age BETWEEN 18 AND 24 THEN '18-24'
                        WHEN age BETWEEN 25 AND 34 THEN '25-34'
                        WHEN age BETWEEN 35 AND 44 THEN '35-44'
                        WHEN age BETWEEN 45 AND 54 THEN '45-54'
                        ELSE '55+' END as age_group"),
                    DB::raw('count(*) as count')
                )
                ->groupBy('age_group')
                ->get();

            $locationDist = (clone $baseQuery)
                ->whereNotNull('location')
                ->select('location', DB::raw('count(*) as count'))
                ->groupBy('location')
                ->orderByDesc('count')
                ->limit(10)
                ->get();

            return response()->json([
                'matched_portraits' => $matchedCount,
                'tag_ids' => $tagIds,
                'gender_distribution' => $genderDist,
                'age_distribution' => $ageDist,
                'location_distribution' => $locationDist,
            ]);
        });
    }
}
