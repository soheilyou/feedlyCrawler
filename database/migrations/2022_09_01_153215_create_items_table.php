<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create("items", function (Blueprint $table) {
            $table->id();
            $table->bigInteger("feed_id")->unsigned();
            $table
                ->foreign("feed_id")
                ->references("id")
                ->on("feeds")
                ->onDelete("cascade");
            $table->string("title", 500);
            $table->string("link", 500);
            $table->string("image", 500)->nullable();
            $table->text("description");
            $table->timestamp("pub_date");
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
        Schema::dropIfExists("items");
    }
}
