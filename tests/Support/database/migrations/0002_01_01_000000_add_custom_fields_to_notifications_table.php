<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Esta migration adiciona campos personalizados de exemplo para os testes.
     * Em produção, o usuário deve criar sua própria migration.
     */
    public function up()
    {
        Schema::table('notifications', function (Blueprint $table) {
            $table->string('category')->nullable();
            $table->integer('priority')->nullable();
            $table->unsignedBigInteger('related_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table('notifications', function (Blueprint $table) {
            $table->dropColumn(['category', 'priority', 'related_id']);
        });
    }
};
