<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use App\Models\CrowdfundingProduct;

class CreateCrowdfundingProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('crowdfunding_products', function (Blueprint $table) {
            $table->bigIncrements('id')->comment('主键');
            $table->unsignedBigInteger('product_id')->comment('商品id');
            $table->foreign('product_id')->references('id')->on('products')->ondelete('cascade');
            $table->dateTime('end_at')->comment('结束时间');
            $table->decimal('target_amount', 10, 2)->comment('目标金额');
            $table->decimal('total_amount', 10, 2)->default(0)->comment('已筹得金额');
            $table->unsignedInteger('user_count')->default(0)->comment('参与用户数');
            $table->string('status')->default(CrowdfundingProduct::STATUS_FUNDING)->comment('状态');
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
        Schema::dropIfExists('crowdfunding_products');
    }
}
