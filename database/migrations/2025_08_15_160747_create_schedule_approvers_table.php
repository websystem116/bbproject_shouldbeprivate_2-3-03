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
            $table->unsignedInteger('id');
            $table->string('name', 100)->comment('承認者名');
            $table->string('email')->comment('メールアドレス');
            $table->enum('role', ['admin', 'office', 'manager'])->comment('役割');
            $table->unsignedInteger('school_building_id')->nullable()->comment('校舎ID（null=全校舎）');
            $table->boolean('is_active')->default(true)->comment('有効フラグ');
            $table->text('notes')->nullable()->comment('備考');
            $table->timestamps();
            $table->softDeletes();
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
