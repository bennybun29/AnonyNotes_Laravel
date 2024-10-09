<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForeignKeyToNotesTable extends Migration
{
    public function up()
    {
        Schema::table('notes', function (Blueprint $table) {
            // Check if foreign key already exists to avoid error
            if (!Schema::hasColumn('notes', 'user_id')) {
                $table->unsignedBigInteger('user_id')->after('note_id'); // Uncomment if user_id does not exist
            }

            // Add the foreign key constraint
            $table->foreign('user_id')
                  ->references('user_id')->on('users')
                  ->onDelete('cascade'); // Adjust delete behavior as needed
        });
    }

    public function down()
    {
        Schema::table('notes', function (Blueprint $table) {
            // Drop the foreign key constraint
            $table->dropForeign(['user_id']);
        });
    }
};
