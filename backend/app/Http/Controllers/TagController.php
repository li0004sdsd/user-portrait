<?php
namespace App\Http\Controllers;
use App\Models\Tag;
use App\Models\TagChangeLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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

        $trackable = ['name', 'value', 'weight'];
        $userId = $request->user()->id;
        $oldValues = $tag->only($trackable);

        return DB::transaction(function () use ($tag, $data, $userId, $oldValues, $trackable) {
            $tag->update($data);

            foreach ($trackable as $field) {
                if (array_key_exists($field, $data)) {
                    $old = (string) ($oldValues[$field] ?? '');
                    $new = (string) ($data[$field] ?? '');
                    if ($old !== $new) {
                        TagChangeLog::create([
                            'tag_id'     => $tag->id,
                            'user_id'    => $userId,
                            'field'      => $field,
                            'old_value'  => $oldValues[$field],
                            'new_value'  => $data[$field],
                        ]);
                    }
                }
            }

            return response()->json($tag->load('category'));
        });
    }

    public function destroy(Request $request, Tag $tag) {
        if ($tag->user_id !== $request->user()->id) {
            return response()->json(['message' => 'Forbidden'], 403);
        }
        $tag->delete();
        return response()->json(['message' => 'Deleted']);
    }
}
