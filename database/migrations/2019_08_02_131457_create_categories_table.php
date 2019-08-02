<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCategoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('categories', function (Blueprint $table) {
            $table->bigIncrements('id')->comment('主键');
            $table->string('name')->comment('类目名称');
            $table->unsignedBigInteger('parent_id')->comment('父类目 ID');
            $table->foreign('parent_id')->references('id')->on('categories')->ondelete('cascade');
            $table->unsignedTinyInteger('is_directory')->comment('是否拥有子类目');
            $table->unsignedInteger('level')->comment('当前类目层级');
            $table->string('path')->comment('该类目所有父类目id');
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
        Schema::dropIfExists('categories');
    }
}
