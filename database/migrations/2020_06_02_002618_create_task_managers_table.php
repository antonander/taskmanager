<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTaskManagersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tasks', function (Blueprint $table) {
            $table->string('name',100)->nullable(false);
            $table->time('time_elapsed')->default('00:00:00');
            $table->string('status', 20)->nullable(false);
            $table->datetime('start_datetime')->default(DB::raw('CURRENT_TIMESTAMP'))->nullable(false);
            $table->datetime('end_datetime')->default(DB::raw('CURRENT_TIMESTAMP'))->nullable(false);
            $table->datetime('last_start_datetime')->default(DB::raw('CURRENT_TIMESTAMP'))->nullable(false);
            $table->primary('name', 'primary_name');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tasks');
    }
}
