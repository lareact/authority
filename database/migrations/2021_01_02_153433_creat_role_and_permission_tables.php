<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatRoleAndPermissionTables extends Migration
{
    /**
     * @throws Exception
     */
    public function up()
    {
        Schema::create('roles', function (Blueprint $table) {
            $table->id('id');
            $table->string('name')->unique()->comment('角色');
            $table->string('description')->nullable()->comment('描述');
            $table->timestamps();
        });

        Schema::create('permissions', function (Blueprint $table) {
            $table->id('id');
            $table->string('name')->unique()->comment('权限');
            $table->string('description')->nullable()->comment('描述');
            $table->timestamps();
        });

        Schema::create('role_has_permissions', function (Blueprint $table) {
            $table->unsignedBigInteger('role_id')->comment('角色');
            $table->unsignedBigInteger('permission_id')->comment('权限');

            $table->foreign('permission_id')
                ->references('id')
                ->on('permissions')
                ->onDelete('cascade');

            $table->foreign('role_id')
                ->references('id')
                ->on('roles')
                ->onDelete('cascade');

            $table->primary(['role_id', 'permission_id'], 'pk_role_permission');
        });

        Schema::create('user_has_roles', function (Blueprint $table) {
            $table->unsignedBigInteger('user_id')->comment('用户');
            $table->unsignedBigInteger('role_id')->comment('角色');

            $table->foreign('role_id')
                ->references('id')
                ->on('roles')
                ->onDelete('cascade');

            $table->primary(['user_id', 'role_id'], 'pk_user_role');
        });

        Schema::create('user_has_permissions', function (Blueprint $table) {
            $table->unsignedBigInteger('user_id')->comment('用户');
            $table->unsignedBigInteger('permission_id')->comment('权限');

            $table->foreign('permission_id')
                ->references('id')
                ->on('permissions')
                ->onDelete('cascade');

            $table->primary(['user_id', 'permission_id'], 'pk_user_permission');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('user_has_permissions');
        Schema::drop('user_has_roles');
        Schema::drop('role_has_permissions');
        Schema::drop('permissions');
        Schema::drop('roles');
    }
}
