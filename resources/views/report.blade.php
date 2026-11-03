<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Campus Events Report</title>
    <style>
        * { box-sizing: border-box; }
        body { margin: 0; font-family: "Segoe UI", Arial, sans-serif; background: #f4f1ea; color: #222; }
        header { background: #7a1020; color: #fff; padding: 22px 32px; }
        header h1 { margin: 0 0 4px; font-size: 26px; }
        header p { margin: 0; opacity: .85; }
        main { max-width: 980px; margin: 0 auto; padding: 24px 16px 48px; }
        section { background: #fff; border-radius: 8px; padding: 18px 22px; margin-bottom: 20px; border-left: 6px solid #d4a017; box-shadow: 0 1px 3px rgba(0,0,0,.08); }
        h2 { margin: 0 0 4px; font-size: 18px; color: #7a1020; }
        .label { font-size: 13px; color: #666; margin: 0 0 12px; }
        .label code { background: #f1eee7; padding: 1px 5px; border-radius: 3px; }
        table { width: 100%; border-collapse: collapse; font-size: 14px; }
        th, td { text-align: left; padding: 8px 10px; border-bottom: 1px solid #eee; }
        th { background: #faf7f0; font-weight: 600; }
        .badge { display: inline-block; padding: 2px 10px; border-radius: 12px; font-size: 12px; font-weight: 600; }
        .registered { background: #dff3e4; color: #1d6b34; }
        .waitlisted { background: #fdecc8; color: #8a5a00; }
        h3 { font-size: 15px; margin: 14px 0 6px; }
        .owner { font-size: 15px; line-height: 1.7; }
        .empty { color: #888; font-style: italic; }
    </style>
</head>
<body>
<header>
    <h1>Campus Events Report</h1>
    <p>Week 10: Relational Data Model &amp; Queries (Laravel + PostgreSQL)</p>
</header>

<main>

    {{-- Query 1 --}}
    <section>
        <h2>Query 1: Every venue with the number of events it hosts</h2>
        <p class="label"><code>Venue::withCount('events')->get()</code></p>
        <table>
            <tr><th>Venue</th><th>Location</th><th>Number of events</th></tr>
            @foreach ($venuesWithCounts as $venue)
                <tr><td>{{ $venue->name }}</td><td>{{ $venue->location }}</td><td>{{ $venue->events_count }}</td></tr>
            @endforeach
        </table>
    </section>

    {{-- Query 2 --}}
    <section>
        <h2>Query 2: All events at {{ $chosenVenue->name }}</h2>
        <p class="label"><code>$venue->events</code></p>
        <table>
            <tr><th>Event</th><th>Starts at</th></tr>
            @forelse ($eventsAtVenue as $event)
                <tr><td>{{ $event->title }}</td><td>{{ $event->starts_at->format('M d, Y g:i A') }}</td></tr>
            @empty
                <tr><td colspan="2" class="empty">No events.</td></tr>
            @endforelse
        </table>
    </section>

    {{-- Query 3 --}}
    <section>
        <h2>Query 3: Participants of "{{ $chosenEvent->title }}" with registration status</h2>
        <p class="label"><code>$event->participants</code> + <code>->registration->status</code> (pivot)</p>
        <table>
            <tr><th>Participant</th><th>Email</th><th>Status</th></tr>
            @forelse ($participantsOfEvent as $participant)
                <tr>
                    <td>{{ $participant->name }}</td>
                    <td>{{ $participant->email }}</td>
                    <td><span class="badge {{ $participant->registration->status }}">{{ $participant->registration->status }}</span></td>
                </tr>
            @empty
                <tr><td colspan="3" class="empty">No participants.</td></tr>
            @endforelse
        </table>
    </section>

    {{-- Query 4 --}}
    <section>
        <h2>Query 4: All events {{ $chosenParticipant->name }} has joined</h2>
        <p class="label"><code>$participant->events</code></p>
        <table>
            <tr><th>Event</th><th>Starts at</th><th>Status</th></tr>
            @forelse ($eventsOfParticipant as $event)
                <tr>
                    <td>{{ $event->title }}</td>
                    <td>{{ $event->starts_at->format('M d, Y g:i A') }}</td>
                    <td><span class="badge {{ $event->registration->status }}">{{ $event->registration->status }}</span></td>
                </tr>
            @empty
                <tr><td colspan="3" class="empty">No events joined.</td></tr>
            @endforelse
        </table>
    </section>

    {{-- Query 5 --}}
    <section>
        <h2>Query 5: Events with at least one waitlisted participant</h2>
        <p class="label"><code>Event::whereHas('participants', fn ($q) =&gt; $q-&gt;where('status', 'waitlisted'))</code></p>
        <table>
            <tr><th>Event</th><th>Venue</th></tr>
            @forelse ($eventsWithWaitlist as $event)
                <tr><td>{{ $event->title }}</td><td>{{ $event->venue->name }}</td></tr>
            @empty
                <tr><td colspan="2" class="empty">No events with a waitlist.</td></tr>
            @endforelse
        </table>
    </section>

    {{-- Query 6 --}}
    <section>
        <h2>Query 6: Notes of one event and notes of one venue</h2>
        <p class="label"><code>$event->notes</code> and <code>$venue->notes</code> (polymorphic)</p>

        <h3>Notes of event "{{ $chosenEvent->title }}"</h3>
        <table>
            <tr><th>#</th><th>Note</th></tr>
            @forelse ($notesOfEvent as $note)
                <tr><td>{{ $note->id }}</td><td>{{ $note->body }}</td></tr>
            @empty
                <tr><td colspan="2" class="empty">No notes.</td></tr>
            @endforelse
        </table>

        <h3>Notes of venue "{{ $chosenVenue->name }}"</h3>
        <table>
            <tr><th>#</th><th>Note</th></tr>
            @forelse ($notesOfVenue as $note)
                <tr><td>{{ $note->id }}</td><td>{{ $note->body }}</td></tr>
            @empty
                <tr><td colspan="2" class="empty">No notes.</td></tr>
            @endforelse
        </table>
    </section>

    {{-- Query 7 --}}
    <section>
        <h2>Query 7: What note #{{ $chosenNote->id }} belongs to</h2>
        <p class="label"><code>$note->notable</code> (morphTo)</p>
        <div class="owner">
            <strong>Note:</strong> {{ $chosenNote->body }}<br>
            <strong>Belongs to:</strong> {{ $ownerName }}<br>
            <strong>Kind of thing:</strong> {{ $ownerKind }}
        </div>
    </section>

</main>
</body>
</html>