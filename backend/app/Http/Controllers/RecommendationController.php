<?php
namespace App\Http\Controllers;
use App\Models\UserPortrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class RecommendationController extends Controller {
    public function index(Request $request) {
        $userId = $request->user()->id;

        $recommendations = Cache::remember("recommendations:{$userId}", 600, function () use ($userId) {
            $portraits = UserPortrait::with('tags.category')
                ->where('user_id', $userId)
                ->where('status', 'active')
                ->get();

            $recommendations = [];
            foreach ($portraits as $portrait) {
                $age = $portrait->age;
                $occupation = strtolower($portrait->occupation ?? '');
                $incomeLevel = $portrait->income_level;
                $tagNames = $portrait->tags->pluck('name')->map(fn($n) => strtolower($n))->toArray();
                $tagCount = count($tagNames);

                $channels = match (true) {
                    $age < 25  => ['TikTok', 'Instagram', 'Bilibili'],
                    $age < 35  => ['WeChat', 'Instagram', 'LinkedIn'],
                    $age < 50  => ['WeChat', 'Weibo', 'Email'],
                    $age !== null => ['WeChat', 'Email', 'SMS'],
                    default    => ['WeChat', 'Email', 'Social Media'],
                };

                $contentTypes = [];
                if (in_array('tech', $tagNames) || in_array('technology', $tagNames)) {
                    $contentTypes[] = 'Technical Articles';
                    $contentTypes[] = 'Product Demos';
                }
                if (in_array('fashion', $tagNames) || in_array('lifestyle', $tagNames)) {
                    $contentTypes[] = 'Visual Content';
                    $contentTypes[] = 'Influencer Campaigns';
                }
                if ($incomeLevel && $incomeLevel > 50000) {
                    $contentTypes[] = 'Premium Offers';
                    $contentTypes[] = 'Exclusive Events';
                }
                if (empty($contentTypes)) {
                    $contentTypes = ['Blog Posts', 'Case Studies', 'Email Newsletters'];
                } else {
                    $contentTypes = array_values(array_unique($contentTypes));
                }

                $timing = match (true) {
                    str_contains($occupation, 'student')   => ['Evening (7pm-10pm)', 'Weekend Afternoon'],
                    str_contains($occupation, 'manager'),
                        str_contains($occupation, 'executive') => ['Early Morning (7am-9am)', 'Lunch Break (12pm-1pm)'],
                    default                                  => ['Morning (9am-11am)', 'Evening (7pm-9pm)'],
                };

                $score = 50;
                if ($portrait->age) $score += 10;
                if ($portrait->gender) $score += 5;
                if ($portrait->occupation) $score += 10;
                if ($incomeLevel) $score += 10;
                if ($portrait->location) $score += 5;
                $score += min($tagCount * 5, 20);

                $recommendations[] = [
                    'portrait_id'   => $portrait->id,
                    'portrait_name' => $portrait->name,
                    'channels'      => $channels,
                    'content_types' => $contentTypes,
                    'best_timing'   => $timing,
                    'score'         => $score,
                ];
            }

            usort($recommendations, function ($a, $b) {
                $cmp = $b['score'] <=> $a['score'];
                if ($cmp !== 0) {
                    return $cmp;
                }
                return $a['portrait_id'] <=> $b['portrait_id'];
            });

            return $recommendations;
        });

        return response()->json($recommendations);
    }
}
