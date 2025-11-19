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
        Schema::create('peminjaman', function (Blueprint $table) {
            $table->id();
            $table->foreignId("user_id")->constrained("users")->cascadeOnDelete()->cascadeOnUpdate();
            $table->integer("product_id")->constrained("products")->cascadeOnDelete()->cascadeOnUpdate();
            $table->integer("location_id")->constrained("location")->cascadeOnDelete()->cascadeOnUpdate();
            $table->date("start_date");
            $table->date("end_date");
            $table->enum("status", ["dipinjam", "dikembalikan"])->default("dipinjam");
            $table->integer("qty");
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('peminjaman');
    }
};
