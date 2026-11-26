<?php

namespace App\Http\Requests;

use App\Models\Media;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreMediaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $required = $this->isMethod('PATCH') ? 'sometimes' : 'required';

        return [
            'title' => [
                $required,
                'string',
                'max:255',
                Rule::unique('media', 'title')->ignore($this->route('media')),
            ],
            'genre' => [$required, 'string', 'max:100'],
            'year' => [$required, 'integer', 'between:1900,2100'],
            'category' => [$required, Rule::in(Media::CATEGORIES)],
            'rating' => [$required, 'numeric', 'between:0,10'],
            'total_parts' => [$required, 'integer', 'min:1'],
            'creator' => [$required, 'string', 'max:255'],
        ];
    }

    public function messages(): array
    {
        return [
            'title.required' => 'Judul media wajib diisi',
            'title.unique' => 'Judul media sudah ada di library',
            'genre.required' => 'Genre wajib diisi',
            'year.required' => 'Tahun rilis wajib diisi',
            'year.between' => 'Tahun rilis harus di antara 1900 dan 2100',
            'category.required' => 'Kategori wajib diisi',
            'category.in' => 'Kategori harus salah satu dari: ' . implode(', ', Media::CATEGORIES),
            'rating.required' => 'Rating wajib diisi',
            'rating.between' => 'Rating harus di antara 0 dan 10',
            'total_parts.required' => 'Jumlah episode/chapter/volume wajib diisi',
            'total_parts.min' => 'Jumlah episode/chapter/volume minimal 1',
            'creator.required' => 'Studio/negara asal/penulis wajib diisi',
        ];
    }
}
