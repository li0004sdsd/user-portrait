<?php
namespace App\Http\Controllers;
use App\Models\Tag;
use Illuminate\Http\Request;

class TagController extends Controller {
    public function index(Request $request) {
        return response()->json(
            Tag::with('category')
                ->where('user_id', $request->user()->id)
                ->get()
        );
    }

    public function store(Request $request) {
        $data = $request->validate([
            'tag_category_id' => 'required|integer|exists:tag_categories,id',
            'name' => 'required|string|max:255',
            'value' => 'nullable|string|max:255',
            'weight' => 'nullable|integer|min:1|max:10',
        ]);
        $data['user_id'] = $request->user()->id;
        return response()->json(Tag::create($data)->load('category'), 201);
    }

    public function update(Request $request, Tag $tag) {
        if ($tag->user_id !== $request->user()->id) {
            return response()->json(['message' => 'Forbidden'], 403);
        }
        $data = $request->validate([
            'name' => 'sometimes|string|max:255',
            'value' => 'nullable|string|max:255',
            'weight' => 'nullable|integer|min:1|max:10',
        ]);
        $tag->update($data);
        return response()->json($tag->load('category'));
    }

    public function destroy(Request $request, Tag $tag) {
        if ($tag->user_id !== $request->user()->id) {
            return response()->json(['message' => 'Forbidden'], 403);
        }
        $tag->delete();
        return response()->json(['message' => 'Deleted']);
    }
}
