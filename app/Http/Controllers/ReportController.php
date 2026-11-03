<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Note;
use App\Models\Participant;
use App\Models\Venue;

class ReportController extends Controller
{
    public function index()
    {
        // The "chosen" rows for the queries below.
        $chosenVenue       = Venue::where('name', 'PUPC Gymnasium')->firstOrFail();
        $chosenEvent       = Event::where('title', 'Intramurals Opening')->firstOrFail();
        $chosenParticipant = Participant::where('name', 'Juan Dela Cruz')->firstOrFail();
        $chosenNote        = Note::where('notable_type', 'venue')->orderBy('id')->firstOrFail();

        // Query 1: every venue with the number of events it hosts.
        // withCount('events') adds an "events_count" attribute.
        $venuesWithCounts = Venue::withCount('events')->orderBy('id')->get();

        // Query 2: all events at one chosen venue.
        $eventsAtVenue = $chosenVenue->events()->orderBy('starts_at')->get();

        // Query 3: all participants of one chosen event, with their status.
        // Each participant has ->registration->status (from the pivot table).
        $participantsOfEvent = $chosenEvent->participants()->orderBy('name')->get();

        // Query 4: all events one chosen participant has joined.
        $eventsOfParticipant = $chosenParticipant->events()->orderBy('starts_at')->get();

        // Query 5: all events that have at least one waitlisted participant.
        // whereHas() filters through the relationship (no hand-written JOIN).
        $eventsWithWaitlist = Event::whereHas('participants', function ($query) {
            $query->where('event_participant.status', 'waitlisted');
        })->with('venue')->orderBy('id')->get();

        // Query 6: all notes of one chosen event, and of one chosen venue.
        $notesOfEvent = $chosenEvent->notes()->orderBy('id')->get();
        $notesOfVenue = $chosenVenue->notes()->orderBy('id')->get();

        // Query 7: for one chosen note, what it belongs to and what kind of thing it is.
        $owner     = $chosenNote->notable;                       // an Event or a Venue object
        $ownerName = $owner instanceof Event ? $owner->title : $owner->name;
        $ownerKind = class_basename($owner);                     // "Event" or "Venue"

        return view('report', compact(
            'chosenVenue', 'chosenEvent', 'chosenParticipant', 'chosenNote',
            'venuesWithCounts', 'eventsAtVenue', 'participantsOfEvent',
            'eventsOfParticipant', 'eventsWithWaitlist',
            'notesOfEvent', 'notesOfVenue', 'ownerName', 'ownerKind'
        ));
    }
}