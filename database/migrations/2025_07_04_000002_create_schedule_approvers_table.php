<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateScheduleApproversTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('schedule_approvers', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name', 100)->comment('承認者名');
            $table->string('email', 255)->comment('メールアドレス');
            $table->enum('role', ['admin', 'office', 'manager'])->comment('役割');
            $table->integer('school_building_id')->unsigned()->nullable()->comment('校舎ID（null=全校舎）');
            $table->boolean('is_active')->default(true)->comment('有効フラグ');
            $table->text('notes')->nullable()->comment('備考');
            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->index(['is_active', 'role']);
            $table->index('email');
            $table->index('school_building_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('schedule_approvers');
    }
}
