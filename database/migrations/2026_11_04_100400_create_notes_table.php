<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// Polymorphic table: a note belongs to EITHER an event OR a venue.
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('notes', function (Blueprint $table) {
            $table->id();
            $table->text('body');

            // morphs('notable') creates two columns plus an index:
            //   notable_type  (varchar)  -> 'event' or 'venue'
            //   notable_id    (bigint)   -> the id inside that table
            // There is NO foreign key here: notable_id can point to two
            // different tables, and a FOREIGN KEY can only reference one.
            $table->morphs('notable');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notes');
    }
};
