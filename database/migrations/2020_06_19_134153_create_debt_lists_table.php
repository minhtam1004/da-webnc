<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDebtListsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('debt_lists', function (Blueprint $table) {
            $table->id();
            $table->integer('ownerId');
            $table->integer('otherId');
            $table->bigInteger('debt');
            $table->enum('status', ['created', 'paid', 'deleted'])->default('created');	
            $table->string('note')->nullable();
            $table->string('deleteNote')->nullable();
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
        Schema::dropIfExists('debt_lists');
    }
}
