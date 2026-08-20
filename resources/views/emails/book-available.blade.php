<p>Hi {{ $reservation->user->name }},</p>

<p>Good news — <strong>{{ $reservation->book->title }}</strong> is now available.</p>

<p><a href="{{ route('books.show', $reservation->book) }}">Borrow it now</a> before someone else does.</p>

<p>— Mini Library</p>
