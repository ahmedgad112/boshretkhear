<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->string('discount_type', 20)->default('amount')->after('nightly_rate');
            $table->decimal('discount_value', 14, 2)->default(0)->after('discount_type');
        });

        DB::table('bookings')->update([
            'discount_type' => 'amount',
        ]);

        DB::statement('UPDATE bookings SET discount_value = discount');
    }

    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropColumn(['discount_type', 'discount_value']);
        });
    }
};
