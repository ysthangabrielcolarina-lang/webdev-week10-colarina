<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// Runs AFTER venues, because events.venue_id points to venues.id.
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('events', function (Blueprint $table) {
            $table->id();

            // One-to-many: an event happens at ONE venue.
            // restrictOnDelete() = ON DELETE RESTRICT:
            // PostgreSQL refuses to delete a venue that still has events.
            $table->foreignId('venue_id')
                  ->constrained('venues')
                  ->restrictOnDelete();

            $table->string('title');
            $table->text('description')->nullable();
            $table->dateTime('starts_at');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('events');
    }
};
