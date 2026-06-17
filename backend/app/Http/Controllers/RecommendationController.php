<?php
namespace App\Http\Controllers;
use App\Models\UserPortrait;
use Illuminate\Http\Request;

class RecommendationController extends Controller {
    public function index(Request $request) {
        $userId = $request->user()->id;
        $portraits = UserPortrait::with('tags.category')
            ->where('user_id', $userId)
            ->where('status', 'active')
            ->get();
        $recommendations = [];
        foreach ($portraits as $portrait) {
            $recommendations[] = [
                'portrait_id' => $portrait->id,
                'portrait_name' => $portrait->name,
                'channels' => $this->recommendChannels($portrait),
                'content_types' => $this->recommendContent($portrait),
                'best_timing' => $this->recommendTiming($portrait),
                'score' => $this->calculateScore($portrait),
            ];
        }
        usort($recommendations, fn($a, $b) => $b['score'] <=> $a['score']);
        return response()->json($recommendations);
    }

    private function recommendChannels($portrait) {
        $age = $portrait->age;
        if ($age) {
            if ($age < 25) return ['TikTok', 'Instagram', 'Bilibili'];
            if ($age < 35) return ['WeChat', 'Instagram', 'LinkedIn'];
            if ($age < 50) return ['WeChat', 'Weibo', 'Email'];
            return ['WeChat', 'Email', 'SMS'];
        }
        return ['WeChat', 'Email', 'Social Media'];
    }

    private function recommendContent($portrait) {
        $types = [];
        $tagNames = $portrait->tags->pluck('name')->map(fn($n) => strtolower($n))->toArray();
        if (in_array('tech', $tagNames) || in_array('technology', $tagNames)) {
            $types[] = 'Technical Articles';
            $types[] = 'Product Demos';
        }
        if (in_array('fashion', $tagNames) || in_array('lifestyle', $tagNames)) {
            $types[] = 'Visual Content';
            $types[] = 'Influencer Campaigns';
        }
        if ($portrait->income_level && $portrait->income_level > 50000) {
            $types[] = 'Premium Offers';
            $types[] = 'Exclusive Events';
        }
        return empty($types) ? ['Blog Posts', 'Case Studies', 'Email Newsletters'] : array_values(array_unique($types));
    }

    private function recommendTiming($portrait) {
        $occupation = strtolower($portrait->occupation ?? '');
        if (str_contains($occupation, 'student')) return ['Evening (7pm-10pm)', 'Weekend Afternoon'];
        if (str_contains($occupation, 'manager') || str_contains($occupation, 'executive')) return ['Early Morning (7am-9am)', 'Lunch Break (12pm-1pm)'];
        return ['Morning (9am-11am)', 'Evening (7pm-9pm)'];
    }

    private function calculateScore($portrait) {
        $score = 50;
        if ($portrait->age) $score += 10;
        if ($portrait->gender) $score += 5;
        if ($portrait->occupation) $score += 10;
        if ($portrait->income_level) $score += 10;
        if ($portrait->location) $score += 5;
        $score += min($portrait->tags->count() * 5, 20);
        return $score;
    }
}
