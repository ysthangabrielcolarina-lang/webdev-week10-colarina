<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Note extends Model
{
    protected $fillable = ['body'];

    // Polymorphic inverse: returns the Event OR the Venue this note belongs to,
    // based on the notable_type and notable_id columns.
    public function notable(): MorphTo
    {
        return $this->morphTo();
    }
}