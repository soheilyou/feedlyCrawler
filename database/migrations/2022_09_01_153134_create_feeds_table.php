<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFeedsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create("feeds", function (Blueprint $table) {
            $table
                ->bigInteger("id")
                ->unsigned()
                ->unique();
            $table->string("url");
            $table->string("rss_path")->nullable();
            $table->integer("update_period_in_minute")->nullable();
            $table->timestamp("last_build_date")->nullable();
            $table->timestamp("last_crawled_at")->nullable();
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
        Schema::dropIfExists("feeds");
    }
}
