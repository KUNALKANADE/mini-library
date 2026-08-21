<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreAuthorRequest;
use App\Http\Requests\UpdateAuthorRequest;
use App\Models\Author;

class AuthorController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(Author::class, 'author');
    }

    public function index()
    {
        $authors = Author::withCount('books')->paginate(15);

        return view('authors.index', compact('authors'));
    }

    public function create()
    {
        return view('authors.create');
    }

    public function store(StoreAuthorRequest $request)
    {
        Author::create($request->validated());

        return redirect()->route('authors.index')
            ->with('success', 'Author created successfully.');
    }

    public function show(Author $author)
    {
        $author->load('books');

        return view('authors.show', compact('author'));
    }

    public function edit(Author $author)
    {
        return view('authors.edit', compact('author'));
    }

    public function update(UpdateAuthorRequest $request, Author $author)
    {
        $author->update($request->validated());

        return redirect()->route('authors.index')
            ->with('success', 'Author updated successfully.');
    }

    public function destroy(Author $author)
    {
        $author->delete();

        return redirect()->route('authors.index')
            ->with('success', 'Author deleted successfully.');
    }
}
