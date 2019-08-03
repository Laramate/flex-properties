<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateStringFlexPropertiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('string_flex_properties', function (Blueprint $table) {
            $table->increments('id');
            $table->nullableMorphs('linkable');
            $table->string('name', 50)->nullable();
            $table->string('locale', 10)->nullable();
            $table->string('value');
            $table->timestamps();
            $table->unique(['id', 'linkable_type', 'linkable_id', 'locale'], 'flex_string_unique');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('string_flex_properties');
    }
}
