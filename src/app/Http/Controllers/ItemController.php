<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Item;
use App\Models\Condition;
use App\Models\Category;
use App\Models\Like;
use App\Models\Comment;
use App\Http\Requests\CommentRequest;
use App\Http\Requests\ExhibitionRequest;

class ItemController extends Controller
{
    /**
     * 商品一覧画面
     * ・おすすめ一覧 / マイリスト一覧を切り替える
     * ・商品名検索にも対応する
     */
    public function index(Request $request)
    {
        // タブ情報（おすすめ or マイリスト）を取得する
        $tab = $request->input('tab');

        // 検索キーワードを取得する
        $keyword = $request->input('keyword');

        // 未ログインでマイリストタブを開いた場合は何も表示しない
        if ($tab === 'mylist' && !auth()->check()) {
            return view('items.index', [
                'items' => collect(),
                'tab' => $tab,
                'keyword' => $keyword,
            ]);
        }

        // マイリストタブの場合
        if ($tab === 'mylist') {
            $query = Item::whereHas('likes', function ($query) {
                // 自分がいいねした商品だけを取得する
                $query->where('user_id', auth()->id());
            })
                // ただし、自分が出品した商品は除外する
                ->where('user_id', '!=', auth()->id());
        } else {
            // おすすめタブの場合は商品一覧を取得する
            $query = Item::query();

            // ログイン中なら自分の商品は除外する
            if (auth()->check()) {
                $query->where('user_id', '!=', auth()->id());
            }
        }

        // キーワードが入力されている場合は商品名で部分一致検索する
        if (!empty($keyword)) {
            $query->where('name', 'like', '%' . $keyword . '%');
        }

        // 条件に一致した商品一覧を取得する
        $items = $query->get();

        // 一覧画面へ渡す
        return view('items.index', compact('items', 'tab', 'keyword'));
    }

    /**
     * 商品出品画面
     * ・カテゴリー一覧と商品の状態一覧を表示する
     */
    public function create()
    {
        // カテゴリー一覧を取得する
        $categories = Category::all();

        // 商品状態一覧を取得する
        $conditions = Condition::all();

        // 出品画面へ渡す
        return view('items.create', compact('categories', 'conditions'));
    }

    /**
     * 商品出品処理
     * ・画像を storage/app/public/items に保存する
     * ・商品情報を items テーブルへ保存する
     * ・カテゴリーを中間テーブルへ保存する
     */
    public function store(ExhibitionRequest $request)
    {
        // アップロードされた画像を storage/app/public/items に保存する
        $imagePath = $request->file('image')->store('items', 'public');

        // 商品データを items テーブルへ保存する
        $item = Item::create([
            'user_id' => auth()->id(),
            // 出品者のユーザーIDを保存する

            'condition_id' => $request->condition_id,
            // 商品状態IDを保存する

            'name' => $request->name,
            // 商品名を保存する

            'brand_name' => $request->brand_name,
            // ブランド名を保存する（任意項目）

            'description' => $request->description,
            // 商品説明を保存する

            'price' => $request->price,
            // 価格を保存する

            'image_path' => $imagePath,
            // 保存した画像パスをDBへ保存する
        ]);

        // カテゴリーが選択されている場合は中間テーブルへ保存する
        if ($request->has('categories')) {
            $item->categories()->attach($request->categories);
        }

        // 商品一覧画面へ戻る
        return redirect()->route('items.index');
    }

    /**
     * 商品詳細画面
     * ・商品情報
     * ・カテゴリー
     * ・状態
     * ・いいね数
     * ・コメント数
     * ・ログインユーザーがいいね済みかどうか
     * を表示する
     */
    public function show($item_id)
    {
        // 関連データもまとめて取得する
        $item = Item::with([
            'condition',
            'categories',
            'likes',
            'comments.user',
        ])->findOrFail($item_id);

        // いいね数を取得する
        $likeCount = $item->likes->count();

        // コメント数を取得する
        $commentCount = $item->comments->count();

        // 初期値では「いいねしていない」にしておく
        $isLiked = false;

        // ログイン中の場合だけ、いいね済みかどうか確認する
        if (auth()->check()) {
            $isLiked = $item->likes()
                ->where('user_id', auth()->id())
                ->exists();
        }

        // 商品詳細画面へ渡す
        return view('items.show', compact('item', 'likeCount', 'commentCount', 'isLiked'));
    }

    /**
     * いいね登録処理
     * ・自分の商品にはいいねできない
     * ・同じ商品への重複いいねは firstOrCreate で防ぐ
     */
    public function like($item_id)
    {
        // 対象商品を取得する
        $item = Item::findOrFail($item_id);

        // 自分の商品にはいいねできないので前画面へ戻る
        if (auth()->id() === $item->user_id) {
            return back();
        }

        // すでに存在しない場合のみ いいね を作成する
        Like::firstOrCreate([
            'user_id' => auth()->id(),
            'item_id' => $item_id,
        ]);

        // 前画面へ戻る
        return back();
    }

    /**
     * いいね解除処理
     * ・自分の商品は対象外
     */
    public function unlike($item_id)
    {
        // 対象商品を取得する
        $item = Item::findOrFail($item_id);

        // 自分の商品には処理しない
        if (auth()->id() === $item->user_id) {
            return back();
        }

        // 対象のいいねを削除する
        Like::where('user_id', auth()->id())
            ->where('item_id', $item_id)
            ->delete();

        // 前画面へ戻る
        return back();
    }

    /**
     * コメント投稿処理
     * ・対象商品が存在することを確認してから保存する
     */
    public function storeComment(CommentRequest $request, $item_id)
    {
        // 商品が存在するか確認する
        $item = Item::findOrFail($item_id);

        // コメントを保存する
        Comment::create([
            'user_id' => auth()->id(),
            'item_id' => $item->id,
            'content' => $request->content,
        ]);

        // 商品詳細画面へ戻る
        return redirect()->route('items.show', ['item_id' => $item->id]);
    }
}