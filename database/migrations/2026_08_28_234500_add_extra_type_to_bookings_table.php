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
            $table->string('extra_type', 20)->default('amount')->after('discount');
            $table->decimal('extra_value', 14, 2)->default(0)->after('extra_type');
        });

        DB::table('bookings')->update([
            'extra_type' => 'amount',
        ]);

        DB::statement('UPDATE bookings SET extra_value = extra_amount');
    }

    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropColumn(['extra_type', 'extra_value']);
        });
    }
};
