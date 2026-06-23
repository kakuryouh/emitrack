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
        Schema::create('travellogs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('fuel_id')->nullable()->constrained('fuels')->onDelete('cascade');
            $table->date('log_date');
            $table->enum('transport_type', ['private', 'public']);
            $table->string('transport_mode')->nullable();
            $table->string('origin')->nullable();
            $table->string('destination')->nullable();
            $table->float('distance_km', 14);
            $table->float('emissions_g', 14);
            $table->float('cost_rp', 14);
            $table->float('money_saved_rp', 14)->default(0); 
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('travellogs');
    }
};
