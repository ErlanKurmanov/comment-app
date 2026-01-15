<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreCommentRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'body' => ['required', 'string', 'max:1000'],
            'parent_id' => ['nullable', 'exists:comments,id'],
            'commentable_type' => ['required', Rule::in(['news', 'video'])],
            'commentable_id' => ['required', 'integer', function ($attribute, $value, $fail) {
                $type = $this->input('commentable_type');
                $modelClass = match ($type) {
                    'news' => \App\Models\News::class,
                    'video' => \App\Models\VideoPost::class,
                    default => null,
                };

                if (!$modelClass || !$modelClass::where('id', $value)->exists()) {
                    $fail("The selected content not found.");
                }
            }],
        ];
    }
}
