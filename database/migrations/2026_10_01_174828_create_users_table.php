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
        Schema::create('users', function (Blueprint $table) {
            $table->id(); // id (Primary Key)
            $table->string('google_id')->nullable()->unique(); // Menyimpan Google ID jika login via Google
            $table->string('name'); // Nama pengguna
            $table->string('email')->unique(); // Email pengguna
            $table->timestamp('email_verified_at')->nullable(); // Bawaan Laravel (opsional)
            $table->string('avatar_url')->nullable(); // Foto profil / avatar
            $table->string('gender')->nullable(); // Jenis kelamin (Male/Female/Both)
            $table->string('country')->nullable(); // Negara asal (misal: Indonesia)
            $table->string('password')->nullable(); // Password (nullable jika murni pakai Google Login)
            $table->rememberToken(); // Bawaan Laravel
            $table->timestamps(); // created_at dan updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};