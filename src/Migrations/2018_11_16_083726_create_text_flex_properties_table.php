<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTextFlexPropertiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('text_flex_properties', function (Blueprint $table) {
            $table->increments('id');
            $table->morphs('linkable');
            $table->string('name', 50)->nullable();
            $table->string('locale', 5)->nullable()->index();
            $table->longText('value');
            $table->timestamps();
            $table->unique(['id', 'linkable_type', 'linkable_id', 'locale'], 'flex_text_unique');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('text_flex_properties');
    }
}
