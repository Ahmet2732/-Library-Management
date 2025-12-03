<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('task', function (Blueprint $table) {
            $table->string('title')->after('id');
            $table->text('description')->nullable()->after('title');
            $table->enum('status', ['pending', 'in_progress', 'completed'])->default('pending')->after('description');
            $table->date('due_date')->nullable()->after('status');
            $table->enum('priority', ['low', 'medium', 'high'])->default('medium')->after('due_date');
        });
    }

    public function down(): void
    {
        Schema::table('task', function (Blueprint $table) {
            $table->dropColumn(['title', 'description', 'status', 'due_date', 'priority']);
        });
    }
};
