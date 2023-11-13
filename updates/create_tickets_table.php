<?php namespace Waka\Support\Updates;

use Winter\Storm\Database\Schema\Blueprint;
use Winter\Storm\Database\Updates\Migration;
use Schema;

class CreateTicketsTable extends Migration
{
    public function up()
    {
        Schema::create('waka_support_tickets', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->boolean('silent_mode')->nullable();
            $table->string('name');
            $table->integer('ticket_type_id')->unsigned();
            $table->string('state')->nullable();
            $table->string('wf')->nullable();
            $table->integer('user_id')->unsigned();
            $table->boolean('urgent')->nullable();
            $table->string('code')->nullable();
            $table->integer('support_user_id')->unsigned()->nullable();
            $table->integer('support_client_id')->unsigned()->nullable();
            $table->integer('next_id')->unsigned()->nullable();
            $table->integer('ticket_group_id')->unsigned()->nullable();
            $table->date('awake_at')->nullable();
            $table->string('url')->nullable();
            $table->double('temps', 15, 2)->default(0);
            //simpletree
            $table->integer('parent_id')->unsigned()->nullable();
            //reorder
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('waka_support_tickets');
    }
}