<p>Hello {{ $result->registration->team->captain->name }},</p>
<p>The {{ $result->round->name }} results for {{ $result->registration->competition->name }} (team {{ $result->registration->team->name }}) have been published.</p>
<p>Your team status: <strong>{{ $result->status->value === 'QUALIFIED' ? 'QUALIFIED' : 'NOT QUALIFIED' }}</strong></p>
<p>Check your dashboard for details. Thank you for participating in Catalyst.</p>
