<?php
namespace App\Http\Controllers;
use App\Models\UserPortrait;
use App\Models\Tag;
use App\Models\TagCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class AnalysisController extends Controller {
    public function index(Request $request) {
        $userId = $request->user()->id;

        return response()->json(array_merge(
            $this->getSummaryStats($userId),
            [
                'gender_distribution'   => $this->getGenderDistribution($userId),
                'age_distribution'      => $this->getAgeDistribution($userId),
                'location_distribution' => $this->getLocationDistribution($userId),
                'top_tags'              => $this->getTopTags($userId),
            ]
        ));
    }

    private function getSummaryStats(int $userId): array {
        $totalPortraits = UserPortrait::where('user_id', $userId)->count();
        $activePortraits = UserPortrait::where('user_id', $userId)->where('status', 'active')->count();
        $totalTags = Tag::where('user_id', $userId)->count();
        $totalCategories = TagCategory::where('user_id', $userId)->count();

        return [
            'total_portraits'    => $totalPortraits,
            'active_portraits'   => $activePortraits,
            'inactive_portraits' => $totalPortraits - $activePortraits,
            'total_tags'         => $totalTags,
            'total_categories'   => $totalCategories,
        ];
    }

    private function getGenderDistribution(int $userId) {
        return UserPortrait::where('user_id', $userId)
            ->whereNotNull('gender')
            ->select('gender', DB::raw('count(*) as count'))
            ->groupBy('gender')
            ->get();
    }

    private function getAgeDistribution(int $userId) {
        return UserPortrait::where('user_id', $userId)
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
    }

    private function getLocationDistribution(int $userId) {
        return UserPortrait::where('user_id', $userId)
            ->whereNotNull('location')
            ->select('location', DB::raw('count(*) as count'))
            ->groupBy('location')
            ->orderByDesc('count')
            ->limit(10)
            ->get();
    }

    private function getTopTags(int $userId) {
        return DB::table('portrait_tags')
            ->join('tags', 'portrait_tags.tag_id', '=', 'tags.id')
            ->join('user_portraits', 'portrait_tags.user_portrait_id', '=', 'user_portraits.id')
            ->where('user_portraits.user_id', $userId)
            ->select('tags.name', DB::raw('count(*) as count'))
            ->groupBy('tags.id', 'tags.name')
            ->orderByDesc('count')
            ->limit(10)
            ->get();
    }

    public function filter(Request $request) {
        $userId = $request->user()->id;
        $data = $request->validate([
            'tag_ids' => 'required|array|min:1',
            'tag_ids.*' => ['integer', Rule::exists('tags', 'id')->where('user_id', $userId)],
        ]);

        $tagIds = $data['tag_ids'];
        sort($tagIds);
        $cacheKey = "analysis:filter:{$userId}:" . implode(',', $tagIds);

        return Cache::remember($cacheKey, 300, function () use ($userId, $tagIds) {
            $baseQuery = UserPortrait::where('user_id', $userId)
                ->whereHas('tags', function ($q) use ($tagIds) {
                    $q->whereIn('tags.id', $tagIds);
                }, '=', count($tagIds));

            return response()->json([
                'matched_portraits'   => (clone $baseQuery)->count(),
                'tag_ids'             => $tagIds,
                'gender_distribution' => $this->getFilteredGenderDistribution($baseQuery),
                'age_distribution'    => $this->getFilteredAgeDistribution($baseQuery),
                'location_distribution' => $this->getFilteredLocationDistribution($baseQuery),
            ]);
        });
    }

    private function getFilteredGenderDistribution($baseQuery) {
        return (clone $baseQuery)
            ->whereNotNull('gender')
            ->select('gender', DB::raw('count(*) as count'))
            ->groupBy('gender')
            ->get();
    }

    private function getFilteredAgeDistribution($baseQuery) {
        return (clone $baseQuery)
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
    }

    private function getFilteredLocationDistribution($baseQuery) {
        return (clone $baseQuery)
            ->whereNotNull('location')
            ->select('location', DB::raw('count(*) as count'))
            ->groupBy('location')
            ->orderByDesc('count')
            ->limit(10)
            ->get();
    }
}
