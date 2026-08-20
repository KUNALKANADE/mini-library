<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BookResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'isbn' => $this->isbn,
            'description' => $this->description,
            'author' => $this->author->name,
            'category' => $this->category->name,
            'total_copies' => $this->total_copies,
            'available_copies' => $this->available_copies,
            'is_available' => $this->isAvailable(),
        ];
    }
}
