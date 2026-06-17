<?php
namespace App\Http\Controllers;
use App\Models\UserPortrait;
use Illuminate\Http\Request;

class UserPortraitController extends Controller {
    public function index(Request $request) {
        $portraits = UserPortrait::with('tags.category')
            ->where('user_id', $request->user()->id)
            ->latest()
            ->paginate(15);
        return response()->json($portraits);
    }

    public function store(Request $request) {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'age' => 'nullable|integer|min:1|max:120',
            'gender' => 'nullable|string',
            'occupation' => 'nullable|string|max:255',
            'location' => 'nullable|string|max:255',
            'income_level' => 'nullable|numeric|min:0',
            'interests' => 'nullable|string',
            'pain_points' => 'nullable|string',
            'goals' => 'nullable|string',
            'status' => 'nullable|string|in:active,inactive',
            'tag_ids' => 'nullable|array',
            'tag_ids.*' => 'integer|exists:tags,id',
        ]);
        $tagIds = $data['tag_ids'] ?? [];
        unset($data['tag_ids']);
        $data['user_id'] = $request->user()->id;
        $portrait = UserPortrait::create($data);
        if (!empty($tagIds)) {
            $portrait->tags()->sync($tagIds);
        }
        return response()->json($portrait->load('tags.category'), 201);
    }

    public function show(Request $request, UserPortrait $userPortrait) {
        if ($userPortrait->user_id !== $request->user()->id) {
            return response()->json(['message' => 'Forbidden'], 403);
        }
        return response()->json($userPortrait->load('tags.category'));
    }

    public function update(Request $request, UserPortrait $userPortrait) {
        if ($userPortrait->user_id !== $request->user()->id) {
            return response()->json(['message' => 'Forbidden'], 403);
        }
        $data = $request->validate([
            'name' => 'sometimes|string|max:255',
            'age' => 'nullable|integer|min:1|max:120',
            'gender' => 'nullable|string',
            'occupation' => 'nullable|string|max:255',
            'location' => 'nullable|string|max:255',
            'income_level' => 'nullable|numeric|min:0',
            'interests' => 'nullable|string',
            'pain_points' => 'nullable|string',
            'goals' => 'nullable|string',
            'status' => 'nullable|string|in:active,inactive',
            'tag_ids' => 'nullable|array',
            'tag_ids.*' => 'integer|exists:tags,id',
        ]);
        $tagIds = $data['tag_ids'] ?? null;
        unset($data['tag_ids']);
        $userPortrait->update($data);
        if ($tagIds !== null) {
            $userPortrait->tags()->sync($tagIds);
        }
        return response()->json($userPortrait->load('tags.category'));
    }

    public function destroy(Request $request, UserPortrait $userPortrait) {
        if ($userPortrait->user_id !== $request->user()->id) {
            return response()->json(['message' => 'Forbidden'], 403);
        }
        $userPortrait->delete();
        return response()->json(['message' => 'Deleted']);
    }
}
