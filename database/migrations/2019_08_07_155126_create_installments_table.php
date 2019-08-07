<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateInstallmentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('installments', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('no')->unique()->comment('分期流水号');
            $table->unsignedBigInteger('user_id')->comment('用户id');
            $table->foreign('user_id')->references('id')->on('users')->ondelete('cascade');
            $table->unsignedBigInteger('order_id')->comment('订单id');
            $table->foreign('order_id')->references('id')->on('orders')->ondelete('cascade');
            $table->decimal('total_amount')->comment('总本金');
            $table->unsignedInteger('count')->comment('期数');
            $table->float('fee_rate')->comment('手续费率');
            $table->float('fine_rate')->comment('逾期费率');
            $table->string('status')->default(\App\Models\installment::STATUS_PENDING)->comment('状态');
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
        Schema::dropIfExists('installments');
    }
}
