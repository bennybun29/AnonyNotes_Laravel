<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('notes', function (Blueprint $table) {
            // Add the 'user_id' column as a foreign key
            $table->unsignedBigInteger('user_id')->after('note_id'); // Adjust the position if necessary
    
            // Add foreign key constraint
            $table->foreign('user_id')->references('user_id')->on('users')->onDelete('cascade');
        });
    }
    
    public function down()
    {
        Schema::table('notes', function (Blueprint $table) {
            // Drop foreign key and the column
            $table->dropForeign(['user_id']);
            $table->dropColumn('user_id');
        });
    }
    
};
