<p>Hi {{ $loan->user->name }},</p>

<p><strong>{{ $loan->book->title }}</strong> was due on {{ $loan->due_at->format('F j, Y') }} and hasn't been returned yet.</p>

<p>Please return it as soon as possible.</p>

<p>— Mini Library</p>
