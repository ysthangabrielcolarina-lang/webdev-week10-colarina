<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Venue extends Model
{
    protected $fillable = ['name', 'location', 'capacity'];

    // One-to-many: a venue hosts many events (events.venue_id -> venues.id).
    public function events(): HasMany
    {
        return $this->hasMany(Event::class);
    }

    // Polymorphic one-to-many: a venue can have many notes.
    public function notes(): MorphMany
    {
        return $this->morphMany(Note::class, 'notable');
    }
}