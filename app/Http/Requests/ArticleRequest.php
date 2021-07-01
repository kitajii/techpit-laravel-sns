<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ArticleRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'title' => 'required|max:50',
            'body' => 'required|max:500',
            'tags' => 'json|regex:/^(?!.*\s).+$/u|regex:/^(?!.*\/).*$/u', //json形式かどうか(vueからjson形式で渡される為), 正規表現でスペース禁止, 正規表現で"/"禁止 の3つ
        ];
    }

    public function attributes()
    {
        return [
            'title' => 'タイトル',
            'body' => '本文',
            'tags' => 'タグ',
        ];
    }

    public function passedValidation()
    {
        $this->tags = collect(json_decode($this->tags))
        ->slice(0, 5)
        ->map(function ($requestTag) {
            return $requestTag->text;
        });
    }

}
