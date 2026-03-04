<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
   public function up(): void
    {
        Schema::table('slideshows', function (Blueprint $table) {
            $table->unsignedInteger('sort_order')->default(0)->after('image');
            $table->boolean('is_active')->default(true)->after('sort_order');
            $table->string('link_url', 2000)->nullable()->after('is_active');
        });
    }

    public function down(): void
    {
        Schema::table('slideshows', function (Blueprint $table) {
            $table->dropColumn(['sort_order', 'is_active', 'link_url']);
        });
    }
};
