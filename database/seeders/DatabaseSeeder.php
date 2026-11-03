<?php

namespace Database\Seeders;

use App\Models\Participant;
use App\Models\Venue;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // ---------- 3 venues ----------
        $gym = Venue::create(['name' => 'PUPC Gymnasium', 'location' => 'Main Campus, Calauan', 'capacity' => 800]);
        $avr = Venue::create(['name' => 'Audio-Visual Room', 'location' => 'Academic Building, 2nd Floor', 'capacity' => 120]);
        $court = Venue::create(['name' => 'Covered Court', 'location' => 'Beside the Admin Building', 'capacity' => 500]);

        // ---------- 6 events, created THROUGH the venue relationship ----------
        // $gym->events()->create([...]) fills in venue_id automatically.
        $e1 = $gym->events()->create(['title' => 'Intramurals Opening', 'description' => 'Parade and opening program.', 'starts_at' => '2026-11-16 08:00']);
        $e2 = $gym->events()->create(['title' => 'Basketball Finals', 'description' => 'Championship game.', 'starts_at' => '2026-11-20 13:00']);
        $e3 = $gym->events()->create(['title' => 'Cheerdance Competition', 'description' => 'Per-department cheerdance.', 'starts_at' => '2026-11-21 15:00']);
        $e4 = $avr->events()->create(['title' => 'Web Development Seminar', 'description' => 'Intro to Laravel.', 'starts_at' => '2026-11-24 09:00']);
        $e5 = $avr->events()->create(['title' => 'Database Workshop', 'description' => 'Hands-on PostgreSQL.', 'starts_at' => '2026-11-25 09:00']);
        $e6 = $court->events()->create(['title' => 'Foundation Day Concert', 'description' => 'Evening concert.', 'starts_at' => '2026-12-01 18:00']);

        // ---------- 8 participants ----------
        $names = [
            'Juan Dela Cruz', 'Maria Santos', 'Jose Reyes', 'Ana Garcia',
            'Mark Villanueva', 'Liza Mendoza', 'Paolo Ramos', 'Camille Torres',
        ];
        $p = [];
        foreach ($names as $i => $name) {
            $p[$i + 1] = Participant::create([
                'name'  => $name,
                'email' => strtolower(str_replace(' ', '.', $name)) . '@example.com',
            ]);
        }

        // ---------- 14 registrations, created THROUGH the many-to-many relationship ----------
        // attach() inserts rows into the event_participant pivot table.
        $e1->participants()->attach([
            $p[1]->id => ['status' => 'registered'],
            $p[2]->id => ['status' => 'registered'],
            $p[3]->id => ['status' => 'registered'],
            $p[4]->id => ['status' => 'waitlisted'],
        ]);
        $e2->participants()->attach([
            $p[1]->id => ['status' => 'registered'],
            $p[5]->id => ['status' => 'registered'],
        ]);
        $e3->participants()->attach([
            $p[2]->id => ['status' => 'registered'],
            $p[6]->id => ['status' => 'waitlisted'],
        ]);
        $e4->participants()->attach([
            $p[7]->id => ['status' => 'registered'],
        ]);
        $e5->participants()->attach([
            $p[1]->id => ['status' => 'registered'],
            $p[8]->id => ['status' => 'registered'],
        ]);
        $e6->participants()->attach([
            $p[3]->id => ['status' => 'registered'],
            $p[4]->id => ['status' => 'registered'],
            $p[5]->id => ['status' => 'waitlisted'],
        ]);

        // ---------- 6 notes, created THROUGH the polymorphic relationship ----------
        // 3 on events
        $e1->notes()->create(['body' => 'Assembly at 7:30 AM in PE uniform.']);
        $e1->notes()->create(['body' => 'Each department brings its own banner.']);
        $e2->notes()->create(['body' => 'Free entrance for students with ID.']);
        // 3 on venues
        $gym->notes()->create(['body' => 'Aircon units are under maintenance until November 10.']);
        $gym->notes()->create(['body' => 'Reserve through the Student Affairs Office.']);
        $avr->notes()->create(['body' => 'Projector remote is kept at the faculty room.']);
    }
}