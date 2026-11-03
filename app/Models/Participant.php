<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Participant extends Model
{
    protected $fillable = ['name', 'email'];

    // Many-to-many (other side): a participant joins many events.
    public function events(): BelongsToMany
    {
        return $this->belongsToMany(Event::class, 'event_participant')
                    ->withPivot('status')
                    ->withTimestamps()
                    ->as('registration');
    }
}