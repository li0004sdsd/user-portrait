<?php
namespace App\Http\Controllers;
use App\Models\TagCategory;
use Illuminate\Http\Request;

class TagCategoryController extends Controller {
    public function index(Request $request) {
        return response()->json(
            TagCategory::with('tags')
                ->where('user_id', $request->user()->id)
                ->get()
        );
    }

    public function store(Request $request) {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'color' => 'nullable|string|max:20',
            'description' => 'nullable|string',
        ]);
        $data['user_id'] = $request->user()->id;
        return response()->json(TagCategory::create($data), 201);
    }

    public function update(Request $request, TagCategory $tagCategory) {
        if ($tagCategory->user_id !== $request->user()->id) {
            return response()->json(['message' => 'Forbidden'], 403);
        }
        $data = $request->validate([
            'name' => 'sometimes|string|max:255',
            'color' => 'nullable|string|max:20',
            'description' => 'nullable|string',
        ]);
        $tagCategory->update($data);
        return response()->json($tagCategory->load('tags'));
    }

    public function destroy(Request $request, TagCategory $tagCategory) {
        if ($tagCategory->user_id !== $request->user()->id) {
            return response()->json(['message' => 'Forbidden'], 403);
        }
        $tagCategory->delete();
        return response()->json(['message' => 'Deleted']);
    }
}
