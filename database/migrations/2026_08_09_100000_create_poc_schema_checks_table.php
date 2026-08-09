<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * v0.2.0 更新検証用テーブル。
     *
     * このmigrationはv0.1→v0.2更新時に新しいmigrationが
     * 正しく適用されることを証明するために存在する。
     * 業務仕様には影響しない。
     */
    public function up(): void
    {
        Schema::create('poc_schema_checks', function (Blueprint $table): void {
            $table->id();
            $table->string('version');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('poc_schema_checks');
    }
};
