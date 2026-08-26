<?php

namespace Tests\Feature;

use App\Models\Author;
use App\Models\Book;
use App\Models\Category;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Excel;
use Tests\TestCase;

class BookImportTest extends TestCase
{
    use RefreshDatabase;

    public function test_librarian_can_import_books_from_excel(): void
{
    $this->withoutExceptionHandling();

    $librarian = User::factory()->librarian()->create();

    $file = UploadedFile::fake()->createWithContent(
        'books.csv',
        "title,isbn,author,category,copies\n" .
        "Clean Code,9780132350884,Robert Martin,Software Engineering,3\n"
    );

    $response = $this->actingAs($librarian)
        ->post(route('books.import'), ['file' => $file]);

    $response->assertRedirect(route('books.index'));
    $this->assertDatabaseHas('books', ['title' => 'Clean Code']);
    $this->assertDatabaseHas('authors', ['name' => 'Robert Martin']);
    $this->assertDatabaseHas('categories', ['name' => 'Software Engineering']);
}

    public function test_member_cannot_import_books(): void
    {
        $member = User::factory()->create();

        $file = UploadedFile::fake()->createWithContent(
            'books.csv',
            "title,isbn,author,category,copies\nTest Book,123,Some Author,Fiction,1\n"
        );

        $response = $this->actingAs($member)
            ->post(route('books.import'), ['file' => $file]);

        $response->assertForbidden();
    }

    public function test_reusing_an_existing_author_does_not_create_a_duplicate(): void
    {
        $librarian = User::factory()->librarian()->create();
        Author::factory()->create(['name' => 'Robert Martin']);

        $file = UploadedFile::fake()->createWithContent(
            'books.csv',
            "title,isbn,author,category,copies\nClean Code,123,Robert Martin,Software Engineering,3\n"
        );

        $this->actingAs($librarian)->post(route('books.import'), ['file' => $file]);

        $this->assertEquals(1, Author::where('name', 'Robert Martin')->count());
    }
}
