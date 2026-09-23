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
        Schema::table('tables', function (Blueprint $table) {
            $table->string('qr_token', 36)->nullable()->unique()->after('status');
            $table->boolean('is_active')->default(true)->after('qr_token');
        });

        // Generate unique tokens for existing tables
        $existingTables = DB::table('tables')->whereNull('qr_token')->get();
        foreach ($existingTables as $table) {
            DB::table('tables')
                ->where('id', $table->id)
                ->update(['qr_token' => 'tbl_'.strtolower(Str::random(12))]);
        }

        Schema::table('orders', function (Blueprint $table) {
            $table->foreignId('user_id')->nullable()->change();
            $table->string('order_status', 35)->default('pending')->change();
            $table->string('order_source', 20)->default('pos')->after('order_type');
            $table->string('customer_phone', 30)->nullable()->after('customer_name');
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['order_source', 'customer_phone']);
        });

        Schema::table('tables', function (Blueprint $table) {
            $table->dropColumn(['qr_token', 'is_active']);
        });
    }
};
