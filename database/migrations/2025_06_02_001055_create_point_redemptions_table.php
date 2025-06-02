<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
public function up()
{
    Schema::create('point_redemptions', function (Blueprint $table) {
        $table->id(); // primary key, auto increment
        $table->string('idPembeli');
        $table->integer('points_used');
        $table->string('transaction_id')->nullable();
        $table->timestamps(); // created_at and updated_at
    });
}

public function down()
{
    Schema::dropIfExists('point_redemptions');
}

};
