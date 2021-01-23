<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserAccessTokensTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_access_tokens', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->comment('用户ID');
            $table->string('name')->comment('唯一标记');
            $table->string('token', 100)->unique()->comment('鉴权TOKEN');
            $table->text('abilities')->nullable()->comment('聚合权限');
            $table->timestamp('expired_at')->nullable()->comment('过期时间');
            $table->timestamp('last_used_at')->nullable()->comment('最后使用时间');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('user_access_tokens');
    }
}
