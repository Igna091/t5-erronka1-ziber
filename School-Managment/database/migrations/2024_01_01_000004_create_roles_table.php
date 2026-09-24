<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('roles', function (Blueprint $table) {
            $table->id();
            $table->string('name', 50)->unique(); // admin, teacher, student
            $table->timestamps();
        });

        DB::table('roles')->insert([
            ['name' => 'admin', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'teacher', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'student', 'created_at' => now(), 'updated_at' => now()],
        ]);

        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('role_id')->nullable()->after('surname')->constrained('roles')->restrictOnDelete();
        });

        // Move the old text role to the new foreign key
        foreach (DB::table('roles')->pluck('id', 'name') as $name => $id) {
            DB::table('users')->where('role', $name)->update(['role_id' => $id]);
        }

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('role');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('role')->default('student')->after('name');
        });

        foreach (DB::table('roles')->pluck('name', 'id') as $id => $name) {
            DB::table('users')->where('role_id', $id)->update(['role' => $name]);
        }

        Schema::table('users', function (Blueprint $table) {
            $table->dropConstrainedForeignId('role_id');
        });

        Schema::dropIfExists('roles');
    }
};
