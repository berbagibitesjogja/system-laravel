<?php

use App\Models\Volunteer\User;
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
        Schema::create('cancelations', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(User::class);
            $table->date('banned')->nullable();
            $table->integer('tries')->default(1);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cancelations');
    }
};
