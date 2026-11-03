# Campus Events: Week 10 Relational Data Model & Queries

Laravel + PostgreSQL data layer for a small Campus Events system.
Venues host events, participants register for events, and notes can be attached to either an event or a venue.

**Author:** Ysthan Gabriel E. Colarina, BSIT 3

## How to run

```bash
composer install
cp .env.example .env        # then set the DB_* values for PostgreSQL
php artisan key:generate
php artisan migrate:fresh --seed
php artisan serve           # open http://127.0.0.1:8000/report
```

## Tables

| Table | Primary key | Foreign keys |
|---|---|---|
| `venues` | `id` | none |
| `events` | `id` | `venue_id` → `venues.id` |
| `participants` | `id` | none |
| `event_participant` (pivot) | `id` | `event_id` → `events.id`, `participant_id` → `participants.id` (unique together) |
| `notes` | `id` | none (polymorphic `notable_type` + `notable_id`) |

## Relationships

| Link | Type | Model holding the foreign key | Relationship methods | ON DELETE | Reason |
|---|---|---|---|---|---|
| Venue ↔ Event | One-to-many | `Event` (`events.venue_id`) | `Venue::events()` hasMany, `Event::venue()` belongsTo | `RESTRICT` | A venue that still has events must not be deletable. |
| Event ↔ Participant | Many-to-many | Pivot table `event_participant` (`event_id`, `participant_id`, plus `status`) | `Event::participants()` belongsToMany, `Participant::events()` belongsToMany | `CASCADE` (both keys) | When an event or a participant is deleted, its registrations must disappear too. |
| Note → Event / Venue | Polymorphic one-to-many | `Note` (`notes.notable_type` + `notes.notable_id`) | `Event::notes()` morphMany, `Venue::notes()` morphMany, `Note::notable()` morphTo | None (no FK possible) | `notable_id` can point to two different tables, so PostgreSQL cannot enforce it; the application code keeps it valid. |

Other rules enforced by the database:
- `UNIQUE (event_id, participant_id)` on `event_participant`: the same participant cannot register for the same event twice.
- `CHECK (status IN ('registered', 'waitlisted'))` on `event_participant.status`.

## Report page

`GET /report` (`ReportController@index`, view `resources/views/report.blade.php`) shows seven labeled queries, all written with Eloquent relationships:

1. Every venue with the number of events it hosts (`withCount('events')`)
2. All events at one chosen venue (`$venue->events`)
3. All participants of one chosen event with their status (`$event->participants`, pivot `status`)
4. All events one chosen participant has joined (`$participant->events`)
5. All events with at least one waitlisted participant (`whereHas('participants', ...)`)
6. All notes of one chosen event and of one chosen venue (`$event->notes`, `$venue->notes`)
7. For one chosen note, the name and kind of the thing it belongs to (`$note->notable`)

## Seeded data

`php artisan migrate:fresh --seed` creates 3 venues, 6 events, 10 participants, 17 registrations (14 registered, 3 waitlisted) and 6 notes (3 on events, 3 on venues).

## Proof SQL

See `docs/proofs.sql` for the raw SQL used for Steps 7 to 9 (database-enforced errors, the accepted orphan note, and the cascade cleanup).