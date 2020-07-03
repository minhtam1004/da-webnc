<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTransferTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('transfers', function (Blueprint $table) {
            $table->id();
            $table->string('sendId');
            $table->string('sendBank')->nullable();
            $table->string('receivedId');
            $table->string('receivedBank')->nullable();
            $table->bigInteger('amount');
            $table->text('reason');
            $table->boolean('isConfirm')->default(false);
            $table->string('OTPCode')->nullable();
            $table->boolean('payer')->default(true);
            $table->timestamp('expiresAt');
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
        Schema::dropIfExists('transfer');
    }
}
