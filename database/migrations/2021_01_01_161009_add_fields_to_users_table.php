<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Class AddFieldsToUsersTable
 */
class AddFieldsToUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->char('phone', 11)->after('email')->comment('手机号');
            $table->string('group', 64)->index()->after('phone')->comment('用户组');
            $table->string('avatar')->nullable()->after('group')->comment('用户头像');
            $table->boolean('is_active')->default(true)->after('avatar')->comment('是否激活');
            $table->string('inactive_reason')->nullable()->after('is_active')->comment('注销原因');
            $table->boolean('is_tmp')->default(false)->after('inactive_reason')->comment('是否为临时用户');
            $table->timestamp('tmp_started_at')->nullable()->after('is_tmp')->comment('临时账号开始时间');
            $table->timestamp('tmp_ended_at')->nullable()->after('tmp_started_at')->comment('临时账号结束时间');
            $table->string('api_token', 100)->after('password')->nullable()->comment('简单TOKEN');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'phone', 'group', 'avatar', 'is_active', 'inactive_reason',
                'is_tmp', 'tmp_started_at', 'tmp_ended_at', 'api_token'
            ]);
        });
    }
}
