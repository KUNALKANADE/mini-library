<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreBookRequest;
use App\Http\Requests\UpdateBookRequest;
use App\Models\Author;
use App\Models\Book;
use App\Models\Category;
use Illuminate\Http\Request;
use App\Imports\BooksImport;
use Maatwebsite\Excel\Facades\Excel;

class BookController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize('viewAny', Book::class);

        $query = Book::query()->with(['author', 'category']);

        // Search by title
        if ($request->filled('search')) {
            $query->where('title', 'like', '%'.$request->input('search').'%');
        }

        // Filter by author
        if ($request->filled('author_id')) {
            $query->where('author_id', $request->input('author_id'));
        }

        // Filter by category
        if ($request->filled('category_id')) {
            $query->where('category_id', $request->input('category_id'));
        }

        // Filter by availability
        if ($request->input('availability') === 'available') {
            $query->where('available_copies', '>', 0);
        } elseif ($request->input('availability') === 'unavailable') {
            $query->where('available_copies', 0);
        }

        // Sorting
        $sort = $request->input('sort', 'title');
        $direction = $request->input('direction', 'asc');

        $allowedSorts = ['title', 'created_at'];
        if (in_array($sort, $allowedSorts)) {
            $query->orderBy($sort, $direction === 'desc' ? 'desc' : 'asc');
        } elseif ($sort === 'author') {
            $query->join('authors', 'books.author_id', '=', 'authors.id')
                ->orderBy('authors.name', $direction === 'desc' ? 'desc' : 'asc')
                ->select('books.*'); // avoid column collisions from the join
        }

        $books = $query->paginate(15)->withQueryString();

        $authors = Author::orderBy('name')->get();
        $categories = Category::orderBy('name')->get();

        return view('books.index', compact('books', 'authors', 'categories'));
    }

    public function create()
    {
        $this->authorize('create', Book::class);

        $authors = Author::orderBy('name')->get();
        $categories = Category::orderBy('name')->get();

        return view('books.create', compact('authors', 'categories'));
    }

    public function store(StoreBookRequest $request)
    {
        $this->authorize('create', Book::class);

        $data = $request->validated();
        $data['available_copies'] = $data['total_copies'];

        Book::create($data);

        return redirect()->route('books.index')
            ->with('success', 'Book created successfully.');
    }

    public function show(Book $book)
    {
        $this->authorize('view', $book);

        $book->load(['author', 'category']);

        return view('books.show', compact('book'));
    }

    public function edit(Book $book)
    {
        $this->authorize('update', $book);

        $authors = Author::orderBy('name')->get();
        $categories = Category::orderBy('name')->get();

        return view('books.edit', compact('book', 'authors', 'categories'));
    }

    public function update(UpdateBookRequest $request, Book $book)
    {
        $this->authorize('update', $book);

        $book->update($request->validated());

        return redirect()->route('books.index')
            ->with('success', 'Book updated successfully.');
    }

    public function destroy(Book $book)
    {
        $this->authorize('delete', $book);

        $book->delete();

        return redirect()->route('books.index')
            ->with('success', 'Book deleted successfully.');
    }
    public function import(Request $request)
{
    $this->authorize('create', Book::class);

    $request->validate([
        'file' => ['required', 'file', 'mimes:xlsx,xls,csv'],
    ]);

    $import = new BooksImport();
    Excel::import($import, $request->file('file'));

    $failures = $import->errors();

    if ($failures->isNotEmpty()) {
        return back()->with('error', "{$failures->count()} row(s) failed and were skipped. Check the format and try again.");
    }

    return redirect()->route('books.index')
        ->with('success', 'Books imported successfully.');
}
}
