<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateInstallmentItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('installment_items', function (Blueprint $table) {
            $table->bigIncrements('id')->comment('主键');
            $table->unsignedBigInteger('installment_id')->comment('分期主表id');
            $table->unsignedBigInteger('sequence')->comment('当前期数');
            $table->foreign('installment_id')->references('id')->on('installments')->ondelete('cascade');
            $table->decimal('base')->comment('本金');
            $table->decimal('fee')->comment('手续费');
            $table->decimal('fine')->nullable()->comment('逾期费');
            $table->string('payment_method')->nullable()->comment('支付方式');
            $table->string('payment_no')->nullable()->comment('支付流水号');
            $table->dateTime('paid_at')->nullable()->comment('支付时间');
            $table->dateTime('due_date')->comment('逾期时间');
            $table->string('refund_status')->default(\App\Models\InstallmentItem::REFUND_STATUS_PENDING)->comment('退款状态');
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
        Schema::dropIfExists('installment_items');
    }
}
