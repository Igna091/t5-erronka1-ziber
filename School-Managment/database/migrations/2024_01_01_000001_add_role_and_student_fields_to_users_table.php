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
        Schema::table('users', function (Blueprint $table) {
            $table->string('role')->default('student')->after('name'); // admin, student
            $table->string('surname')->nullable()->after('name');
            $table->string('dni')->nullable()->unique()->after('email');
            $table->string('phone')->nullable()->after('dni');
            $table->boolean('is_registered')->default(false)->after('phone');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['role', 'surname', 'dni', 'phone', 'is_registered']);
        });
    }
};
