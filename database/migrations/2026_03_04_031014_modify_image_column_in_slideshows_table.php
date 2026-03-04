<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('slideshows', function (Blueprint $table) {
            $table->text('image')->change();
        });
    }

    public function down()
    {
        Schema::table('slideshows', function (Blueprint $table) {
            $table->string('image')->change();
        });
    }
};
