<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Article;
use App\Tag;
use App\Http\Requests\ArticleRequest;


class ArticleController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(Article::class, 'article');
    }

    public function index()
    {

        $articles = Article::all()->sortByDesc('created_at');

        return view('articles.index', ['articles' => $articles]);
    }

    public function create()
    {
        return view('articles.create');
    }

    public function store(ArticleRequest $request, Article $article)
    {
        $article->fill($request->all());
        $article->user_id = $request->user()->id;
        $article->save();

        $request->tags->each(function ($tagName) use ($article) { //入力されたタグをコレクションから1つずつ取り出し、処理する。$tagNameは取り出された１つを示す。
            $tag = Tag::firstOrCreate(['name' => $tagName]); //入力されたタグが新規か既存か判定する。（Laravel公式関数）
            $article->tags()->attach($tag); //attachで記事に結びついているtagsテーブルのレコードにタグ名を登録
        });
        return redirect()->route('articles.index');
    }

    public function edit(Article $article)
    {
        return view('articles.edit', ['article' => $article]);
    }

    public function update(ArticleRequest $request, Article $article)
    {
        $article->fill($request->all())->save();
        return redirect()->route('articles.index');
    }

    public function destroy(Article $article)
    {
        $article->delete();
        return redirect()->route('articles.index');
    }

    public function show(Article $article)
    {
        return view('articles.show', ['article'=>$article]);
    }

    public function like(Request $request, Article $article)
    {
        $article->likes()->detach($request->user()->id);
        $article->likes()->attach($request->user()->id);

        return [
            'id' => $article->id,
            'countLikes' => $article->count_likes
        ];
    }

    public function unlike(Request $request, Article $article)
    {
        $article->likes()->detach($request->user()->id);
        return [
            'id' => $article->id,
            'countLikes' => $article->count_likes,
        ];
    }


}
