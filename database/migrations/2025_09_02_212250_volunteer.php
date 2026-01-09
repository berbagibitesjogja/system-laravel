<?php

use App\Models\Volunteer\Division;
use App\Models\Volunteer\Faculty;
use App\Models\Volunteer\Precence;
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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->enum('role', ['super', 'core', 'staff', 'member']);
            $table->foreignIdFor(Division::class);
            $table->string('name');
            $table->string('email');
            $table->string('phone');
            $table->string('photo')->nullable();
            $table->integer('point')->default(0);
            $table->timestamps();
        });
        Schema::create('precences', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('description')->nullable();
            $table->string('latitude')->nullable()->default('-7.775326279953496');
            $table->string('longitude')->nullable()->default('110.3778869084333');
            $table->integer('max_distance')->nullable()->default(50);
            $table->char('code', 20);
            $table->enum('status', ['active', 'end'])->default('active');
            $table->timestamps();
        });
        Schema::create('attendances', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Precence::class)->constrained()->onDelete('cascade');
            $table->foreignIdFor(User::class)->constrained()->onDelete('cascade');
            $table->integer('distance');
            $table->integer('point')->nullable();
            $table->unique(['precence_id', 'user_id']);
            $table->timestamps();
        });
        Schema::create('reimburses', function (Blueprint $table) {
            $table->id();
            $table->string('method');
            $table->string('target');
            $table->integer('amount');
            $table->boolean('done')->default(false);
            $table->string('file');
            $table->foreignIdFor(User::class)->constrained()->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
        Schema::dropIfExists('precences');
        Schema::dropIfExists('attendances');
        Schema::dropIfExists('reimburses');
    }
};
