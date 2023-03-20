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
        Schema::create('permissions', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('module_name');
            $table->boolean('default_module_permission')->default(false);
            $table->timestamps();
        });
        Schema::create('permission_associate_models', function (Blueprint $table) {
            $table->unsignedInteger('permission_id');
            $table->string('model');
            $table->timestamps();
        });
        Schema::create('roles', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('homepage_path')->default('/');
            $table->timestamps();
        });
        Schema::create('role_associate_models', function (Blueprint $table) {
            $table->unsignedInteger('role_id');
            $table->string('model');
            $table->timestamps();
        });
        Schema::create('role_has_permissions', function(Blueprint $table){
            $table->unsignedInteger('role_id');
            $table->unsignedInteger('permission_id');
            $table->timestamps();
        });
        Schema::create('user_has_roles', function (Blueprint $table) {
            $table->unsignedInteger('user_id');
            $table->unsignedInteger('role_id');
            $table->boolean('is_default_role')->default(false);
            $table->timestamps();
        });
        Schema::create('user_associate_models', function (Blueprint $table) {
            $table->unsignedInteger('user_id');
            $table->string('model');
            $table->string('associate_identifier');
            $table->timestamps();
        });
        Schema::create('modules', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('path');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('modules');
        Schema::dropIfExists('user_associate_models');
        Schema::dropIfExists('user_has_roles');
        Schema::dropIfExists('role_has_permissions');
        Schema::dropIfExists('role_associate_models');
        Schema::dropIfExists('roles');
        Schema::dropIfExists('permission_associate_models');
        Schema::dropIfExists('permissions');
    }
};
