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
            $table->foreignId("product_id")->constrained("products")->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignId("location_id")->constrained("location")->cascadeOnDelete()->cascadeOnUpdate();
            $table->date("start_date");
            $table->date("end_date");
            $table->string('pin_code');
            $table->text("note")->nullable();
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
