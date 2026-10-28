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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->foreignId('customer_id')->constrained()->restrictOnDelete();
            $table->date('data_evento');
            $table->string('local_evento');
            $table->string('tipo_evento');
            $table->unsignedInteger('convidados');
            $table->string('status')->default('pendente');
            $table->text('observacoes')->nullable();
            $table->decimal('total', 10, 2)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
