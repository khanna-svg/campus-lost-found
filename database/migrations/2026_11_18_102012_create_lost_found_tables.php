<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->enum('type', ['lost', 'found']);
            $table->string('title');
            $table->string('category');
            $table->text('description');
            $table->string('location');
            $table->date('incident_date');
            $table->string('contact_info');
            $table->string('image_url')->nullable();
            $table->enum('status', ['open', 'claimed', 'resolved'])->default('open');
            $table->timestamps();
        });

        Schema::create('claims', function (Blueprint $table) {
            $table->id();
            $table->foreignId('item_id')->constrained()->onDelete('cascade');
            $table->foreignId('claimant_id')->constrained('users')->onDelete('cascade');
            $table->text('proof_description');
            $table->string('evidence_path')->nullable(); // Private bucket: claim-evidence
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('claims');
        Schema::dropIfExists('items');
    }
};