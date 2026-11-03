<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// Pivot table for the many-to-many link between events and participants.
// Runs AFTER both events and participants, since it points to both.
// Name follows Laravel's convention: both table names, singular, alphabetical.
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('event_participant', function (Blueprint $table) {
            $table->id();

            // cascadeOnDelete() = ON DELETE CASCADE:
            // deleting an event or a participant removes its registrations too.
            $table->foreignId('event_id')
                  ->constrained('events')
                  ->cascadeOnDelete();

            $table->foreignId('participant_id')
                  ->constrained('participants')
                  ->cascadeOnDelete();

            // Extra data stored on each registration.
            // On PostgreSQL, enum() becomes varchar + a CHECK constraint.
            $table->enum('status', ['registered', 'waitlisted'])->default('registered');

            $table->timestamps();

            // The same participant cannot register for the same event twice.
            $table->unique(['event_id', 'participant_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('event_participant');
    }
};
