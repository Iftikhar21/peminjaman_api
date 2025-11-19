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
        Schema::create('user_detail', function (Blueprint $table) {
            $table->id();
            $table->foreignId("user_id")->constrained("users")->cascadeOnDelete()->cascadeOnUpdate();
            $table->bigInteger("identity_number");
            $table->string("phone");
            $table->foreignId("status_id")->constrained("status_borrower")->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignId("class_id")->constrained("class")->cascadeOnDelete()->cascadeOnUpdate()->nullable();
            $table->foreignId("major_id")->constrained("major")->cascadeOnDelete()->cascadeOnUpdate()->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_detail');
    }
};
