<p>Hi {{ $loan->user->name }},</p>

<p>You've borrowed <strong>{{ $loan->book->title }}</strong>.</p>

<p>Due back by: {{ $loan->due_at->format('F j, Y') }}</p>

<p>— Mini Library</p>
