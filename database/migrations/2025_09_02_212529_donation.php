<?php

use App\Models\Donation\Donation;
use App\Models\Donation\Sponsor;
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
        Schema::create('sponsors', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->enum('variant', ['company', 'individual'])->nullable();
            $table->string('address')->nullable();
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->boolean('hidden')->default(false);
            $table->timestamps();
        });
        Schema::create('donations', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Sponsor::class)->constrained()->onDelete('restrict');
            $table->integer('quota');
            $table->integer('remain');
            $table->date('take');
            $table->integer('hour')->default(12);
            $table->integer('minute')->default(0);
            $table->string('location')->default('Pusat Studi Pancasila');
            $table->string('maps')->default('https://maps.app.goo.gl/eesnA6CN5fAQrGfP9');
            $table->string('message')->nullable();
            $table->text('notes')->nullable();
            $table->string('media')->nullable();
            $table->enum('status', ['aktif', 'selesai'])->default('aktif');
            $table->json('beneficiaries')->nullable();
            $table->integer('partner_id')->nullable();
            $table->enum('reported', ['sudah'])->nullable();
            $table->addColumn('boolean', 'charity')->nullable()->default(false);
            $table->timestamps();
        });
        Schema::create('foods', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Donation::class)->constrained()->onDelete('cascade');
            $table->string('name');
            $table->string('quantity')->default('1');
            $table->integer('weight')->default(1);
            $table->enum('unit', ['gr', 'ml']);
            $table->string('notes')->nullable(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sponsors');
        Schema::dropIfExists('donations');
        Schema::dropIfExists('foods');
    }
};
