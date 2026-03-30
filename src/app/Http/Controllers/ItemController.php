<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Models\Item;
use App\Models\Condition;
use App\Models\Category;
use App\Models\Like;
use App\Models\Comment;
use App\Http\Requests\CommentRequest;
use App\Http\Requests\ExhibitionRequest;  

class ItemController extends Controller
{
    public function index(Request $request)
    {
        // URLの ?tab=mylist を受け取る
        $tab = $request->tab;

        // 検索キーワードを受け取る
        $keyword = $request->keyword;

        // マイリストタブが押された場合
        if ($tab === 'mylist') {

            // ログイン済みの場合
            if (auth()->check()) {
                $query = Item::whereHas('likes', function ($query) {
                        // 自分がいいねした商品だけ取得
                        $query->where('user_id', auth()->id());
                    })
                    // ただし、自分が出品した商品は表示しない
                    ->where('user_id', '!=', auth()->id());

                // 商品名で部分一致検索
                if (!empty($keyword)) {
                    $query->where('name', 'like', '%' . $keyword . '%');
                }

                $items = $query->get();

            } else {
                // 未ログインの場合はマイリストに何も表示しない
                $items = collect();
            }

        } else {
            // おすすめタブ（通常の商品一覧）

            if (auth()->check()) {
                $query = Item::where('user_id', '!=', auth()->id());
            } else {
                // 未ログイン時は全商品を表示
                $query = Item::query();
            }

            // 商品名で部分一致検索
            if (!empty($keyword)) {
                $query->where('name', 'like', '%' . $keyword . '%');
            }

            $items = $query->get();
        }

        // Blade側でタブの状態と検索キーワードも使えるように渡す
        return view('items.index', compact('items', 'tab', 'keyword'));
    }

    public function create()
    {
        $categories = Category::all();
        $conditions = Condition::all();

        return view('items.create', compact('categories', 'conditions'));
    }

    public function store(ExhibitionRequest $request)
    {
    
        // 画像を storage/app/public/items に保存
        $imagePath = $request->file('image')->store('items', 'public');

        $item = new Item();
        $item->user_id = auth()->id();
        $item->condition_id = $request->condition_id;
        $item->name = $request->name;
        $item->brand_name = $request->brand_name;
        $item->description = $request->description;
        $item->price = $request->price;
        $item->image_path = $imagePath;

        $item->save();

        // カテゴリー保存（中間テーブル）
        if ($request->has('categories')) {
            $item->categories()->attach($request->categories);
        }

        return redirect()->route('items.index');
    }

    public function show($item_id)
    {
        $item = Item::with([
            'condition',
            'categories',
            'likes',
            'comments.user',
        ])->findOrFail($item_id);

        $likeCount = $item->likes->count();
        $commentCount = $item->comments->count();

        $isLiked = false;

        if (auth()->check()) {
            $isLiked = $item->likes()
                ->where('user_id', auth()->id())
                ->exists();
        }

        return view('items.show', compact('item', 'likeCount', 'commentCount', 'isLiked'));
    }

    public function like($item_id)
    {
        $item = Item::findOrFail($item_id);

        if (auth()->id() === $item->user_id) {
            return back();
        }

        Like::firstOrCreate([
            'user_id' => auth()->id(),
            'item_id' => $item_id,
        ]);

        return back();
    }

    public function unlike($item_id)
    {
        $item = Item::findOrFail($item_id);

        if (auth()->id() === $item->user_id) {
            return back();
        }

        Like::where('user_id', auth()->id())
            ->where('item_id', $item_id)
            ->delete();

        return back();
    }

    public function storeComment(CommentRequest $request, $item_id)
    {
        $item = Item::findOrFail($item_id);

        Comment::create([
            'user_id' => auth()->id(),
            'item_id' => $item->id,
            'content' => $request->content,
        ]);

        return redirect()->route('items.show', ['item_id' => $item->id]);
    }
}