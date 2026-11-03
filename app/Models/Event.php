<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Event extends Model
{
    protected $fillable = ['venue_id', 'title', 'description', 'starts_at'];

    protected function casts(): array
    {
        return ['starts_at' => 'datetime'];
    }

    // Inverse of one-to-many: an event happens at one venue.
    public function venue(): BelongsTo
    {
        return $this->belongsTo(Venue::class);
    }

    // Many-to-many through the event_participant pivot table.
    // withPivot('status') lets us read each registration's status.
    // as('registration') renames ->pivot to ->registration for readability.
    public function participants(): BelongsToMany
    {
        return $this->belongsToMany(Participant::class, 'event_participant')
                    ->withPivot('status')
                    ->withTimestamps()
                    ->as('registration');
    }

    // Polymorphic one-to-many: an event can have many notes.
    public function notes(): MorphMany
    {
        return $this->morphMany(Note::class, 'notable');
    }
}