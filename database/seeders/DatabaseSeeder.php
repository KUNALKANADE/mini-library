<?php

namespace Database\Seeders;

use App\Models\Author;
use App\Models\Book;
use App\Models\Category;
use App\Models\Loan;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        User::factory()->admin()->create([
            'name' => 'Admin User',
            'email' => 'admin@mini-library.test',
            'password' => 'Admin@123',
        ]);

        User::factory()->librarian()->create([
            'name' => 'Librarian User',
            'email' => 'librarian@mini-library.test',
        ]);

        $members = User::factory(10)->create(); // defaults to role: member

        $members = User::factory(10)->create();

        $authors = Author::factory(8)->create();
        $categories = Category::factory(5)->create();

        $books = Book::factory(30)->create([
            'author_id' => fn () => $authors->random()->id,
            'category_id' => fn () => $categories->random()->id,
        ]);

        // Some active loans
        Loan::factory(10)->create([
            'user_id' => fn () => $members->random()->id,
            'book_id' => fn () => $books->random()->id,
        ]);

        // A few overdue loans, useful for testing Day 12's overdue command
        Loan::factory(3)->overdue()->create([
            'user_id' => fn () => $members->random()->id,
            'book_id' => fn () => $books->random()->id,
        ]);

        // Some returned loans, for history
        Loan::factory(5)->returned()->create([
            'user_id' => fn () => $members->random()->id,
            'book_id' => fn () => $books->random()->id,
        ]);
    }
}
