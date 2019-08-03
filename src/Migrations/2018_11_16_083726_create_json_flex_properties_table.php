<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateJsonFlexPropertiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('json_flex_properties', function (Blueprint $table) {
            $table->increments('id');
            $table->nullableMorphs('linkable');
            $table->string('name', 50)->nullable();
            $table->string('locale', 10)->nullable();
            $table->longText('value');
            $table->timestamps();
            $table->unique(['id', 'linkable_type', 'linkable_id', 'locale'], 'flex_json_unique');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('json_flex_properties');
    }
}
