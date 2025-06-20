<?php

namespace App\Http\Controllers;

// use App\Models\User;
use App\Models\FlashCardSet;
use App\Models\FlashCardSetDetail;
use App\Http\Requests\FlashCardSetRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FlashCardSetController extends Controller
{
    // 我的單字集清單
    public function index(Request $request)
    {
        $perPage = $request->input('perPage', 25);
        $search = $request->input('search');

        $sets = FlashCardSet::select('id', 'title', 'description', 'author', 'isPublic', 'updated_at')
            ->where('user_id', Auth::id())
            ->when($search, fn($query) => $query->where('title', 'LIKE', '%' . $search . '%'))
            ->withCount('details')
            ->latest()
            ->paginate($perPage);

        if (!$sets) {
            return response()->json(['message' => 'Not Found'], 404);
        }

        return response()->json($sets);
    }

    // 建立單字集
    public function store(FlashCardSetRequest $request)
    {
        $validated = $request->validated();

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

    // 檢視單字集
    public function show(Request $request, $id)
    {
        $perPage = $request->input('perPage');

        $set = FlashCardSet::where('id', $id)->firstOrFail();

        if (!$set) {
            return response()->json(['message' => 'Not Found'], 404);
        }

        if (!$set->isPublic && (!Auth::check() || Auth::id() !== $set->user_id)) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $details = $set->details()->select('word', 'word_description')->paginate($perPage);
        $set->details = $details;

        return response()->json($set);
    }

    // 檢視單字集所有單字
    public function showDetails(Request $request, $id)
    {
        $set = FlashCardSet::where('id', $id)->firstOrFail();

        if (!$set) {
            return response()->json(['message' => 'Not Found'], 404);
        }

        // 私人單字集 且 未登入或非擁有者
        if (!$set->isPublic && (!Auth::check() || Auth::id() !== $set->user_id)) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $set = FlashCardSetDetail::where('flash_card_set_id', $id)->select('word', 'word_description')->get();

        return response()->json($set);
    }

    // 編輯單字集
    public function edit($id)
    {
        $set = FlashCardSet::where('id', $id)
            ->where('user_id', Auth::id())
            ->with(['details' => fn($query) => $query->select('flash_card_set_id', 'word', 'word_description')])
            ->firstOrFail();

        if (!$set) {
            return response()->json(['message' => 'Not Found'], 404);
        }

        return response()->json($set);
    }


    // 更新單字集
    public function update(FlashCardSetRequest $request, $id)
    {
        $validated = $request->validated();

        $set = FlashCardSet::where('user_id', Auth::id())->findOrFail($id);

        if (!$set) {
            return response()->json(['message' => 'Not Found'], 404);
        }

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

    // 刪除單字集
    public function destroy($id)
    {
        $set = FlashCardSet::where('user_id', Auth::id())->findOrFail($id);

        if (!$set) {
            return response()->json(['message' => 'Not Found'], 404);
        }

        $set->delete();

        return response()->json([
            'message' => '單字集刪除成功！'
        ]);
    }

    // 公開單字集清單
    public function publicIndex(Request $request)
    {
        $search = $request->input('search');
        $perPage = $request->input('perPage', 25);

        $sets = FlashCardSet::select('id', 'title', 'description', 'author', 'isPublic', 'updated_at')
            ->where('isPublic', true)
            ->when($search, fn($query) => $query->where('title', 'LIKE', '%' . $search . '%'))
            ->withCount('details')
            ->latest()
            ->paginate($perPage);

        if (!$sets) {
            return response()->json(['message' => 'Not Found'], 404);
        }

        // 遮蔽作者，只顯示前後各3個字元
        $sets->getCollection()->transform(function ($item) {
            $item->author = substr($item->author, 0, 3) . '***' . substr($item->author, -3);
            return $item;
        });

        return response()->json($sets);
    }
}
