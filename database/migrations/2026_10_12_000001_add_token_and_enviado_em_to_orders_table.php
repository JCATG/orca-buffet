<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->string('token', 40)->nullable()->unique()->after('id');
            $table->timestamp('enviado_em')->nullable()->after('status');
        });

        DB::table('orders')->whereNull('token')->pluck('id')->each(function ($id) {
            DB::table('orders')->where('id', $id)->update(['token' => Str::random(40)]);
        });

        DB::table('orders')->where('status', 'em_elaboracao')->update(['status' => 'pendente']);
        DB::table('orders')->where('status', 'finalizado')->update([
            'status' => 'enviado',
            'enviado_em' => DB::raw('updated_at'),
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('orders')->where('status', 'enviado')->update(['status' => 'finalizado']);

        Schema::table('orders', function (Blueprint $table) {
            $table->dropUnique(['token']);
            $table->dropColumn(['token', 'enviado_em']);
        });
    }
};
