<?php

namespace App\Http\Controllers;

use App\Models\FlashCardSet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FlashCardSetController extends Controller
{
    public function index()
    {
        // $sets = FlashCardSet::where('user_id', Auth::id())->with('details')->withCount('details')->get();

        $sets = FlashCardSet::select('id', 'title', 'description', 'author', 'isPublic', 'updated_at')
                            ->where('user_id', Auth::id())
                            ->with(['details'])
                            ->withCount('details')
                            ->get();
        return response()->json($sets);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'author' => 'required|string|max:255',
            'isPublic' => 'required|boolean',
            'details' => 'required|array|min:1',
            'details.*.word' => 'required|string|max:255',
            'details.*.word_description' => 'required|string'
        ], [
            'title.required' => '請輸入標題',
            'title.max' => '標題長度不能超過 255 個字元',
            'details.*.word.required' => '請輸入單字',
            'details.*.word.max' => '單字長度不能超過 255 個字元',
            'details.*.word_description.required' => '請輸入說明'
        ]);

        $set = FlashCardSet::create([
            'user_id' => Auth::id(),
            'title' => $validated['title'],
            'description' => $validated['description'] ?? null,
            'author' => $validated['author'],
            'isPublic' => $validated['isPublic']
        ]);

        foreach ($validated['details'] as $detail) {
            $set->details()->create([
                'word' => $detail['word'],
                'word_description' => $detail['word_description']
            ]);
        }

        return response()->json([
            'message' => '單字集建立成功！',
            'data' => $set
        ]);
    }

    public function show($id)
    {
        $set = FlashCardSet::where('id', $id)->where('user_id', Auth::id())->with('details')->firstOrFail();

        return response()->json($set);
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'author' => 'required|string|max:255',
            'isPublic' => 'required|boolean',
            'details' => 'required|array|min:1',
            'details.*.word' => 'required|string|max:255',
            'details.*.word_description' => 'required|string'
        ], [
            'title.required' => '請輸入標題',
            'title.max' => '標題長度不能超過 255 個字元',
            'details.*.word.required' => '請輸入單字',
            'details.*.word.max' => '單字長度不能超過 255 個字元',
            'details.*.word_description.required' => '請輸入說明'
        ]);

        $set = FlashCardSet::where('user_id', Auth::id())->findOrFail($id);

        $set->update($validated);

        // 刪除全部單字並重新建立
        $set->details()->delete();

        foreach ($validated['details'] as $detail) {
            $set->details()->create([
                'word' => $detail['word'],
                'word_description' => $detail['word_description']
            ]);
        }

        return response()->json([
            'message' => '單字集更新成功！',
            'data' => $set
        ]);
    }

    public function destroy($id)
    {
        $set = FlashCardSet::where('user_id', Auth::id())->findOrFail($id);

        $set->delete();

        return response()->json([
            'message' => '單字集刪除成功！'
        ]);
    }

    public function publicSets()
    {
        $sets = FlashCardSet::where('public', true)->with('details')->get();

        return response()->json($sets);
    }
}
