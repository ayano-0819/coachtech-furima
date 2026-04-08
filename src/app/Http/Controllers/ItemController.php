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
    public function index(Request $request)
    {
        $tab = $request->input('tab');

        $keyword = $request->input('keyword');

        if ($tab === 'mylist' && !auth()->check()) {
            return view('items.index', [
                'items' => collect(),
                'tab' => $tab,
                'keyword' => $keyword,
            ]);
        }

        if ($tab === 'mylist') {
            $query = Item::whereHas('likes', function ($query) {
                $query->where('user_id', auth()->id());
            })
                ->where('user_id', '!=', auth()->id());
        } else {
            $query = Item::query();

            if (auth()->check()) {
                $query->where('user_id', '!=', auth()->id());
            }
        }

        if (!empty($keyword)) {
            $query->where('name', 'like', '%' . $keyword . '%');
        }

        $items = $query->get();

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
        $imagePath = $request->file('image')->store('items', 'public');

        $item = Item::create([
            'user_id' => auth()->id(),
            'condition_id' => $request->condition_id,
            'name' => $request->name,
            'brand_name' => $request->brand_name,
            'description' => $request->description,
            'price' => $request->price,
            'image_path' => $imagePath,
        ]);

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
