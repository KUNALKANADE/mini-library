<?php

namespace App\Imports;

use App\Models\Author;
use App\Models\Book;
use App\Models\Category;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Concerns\SkipsErrors;
use Maatwebsite\Excel\Concerns\SkipsOnError;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class BooksImport implements ToModel, WithHeadingRow, WithValidation, SkipsOnError
{
    use SkipsErrors;

    public function model(array $row): Model|array|null
    {
        $author = Author::firstOrCreate([
            'name' => trim($row['author']),
        ]);

        $categoryName = trim($row['category']);

        $category = Category::firstOrCreate(
            ['name' => $categoryName],
            ['slug' => Str::slug($categoryName)]
        );

        return new Book([
            'title' => $row['title'],
            'isbn' => isset($row['isbn']) ? (string) $row['isbn'] : null,
            'total_copies' => $row['copies'],
            'available_copies' => $row['copies'],
            'author_id' => $author->id,
            'category_id' => $category->id,
        ]);
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'author' => ['required', 'string'],
            'category' => ['required', 'string'],
            'copies' => ['required', 'integer', 'min:1'],
            'isbn' => ['nullable'],
        ];
    }
}
