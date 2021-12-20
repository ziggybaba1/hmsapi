<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAppointmentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('appointments', function (Blueprint $table) {
            $table->id();
            $table->string('appointdate');
            $table->string('serialno');
            $table->text('problem');
            $table->boolean('status')->nullable()->default(false);
            $table->unsignedBigInteger('department_id');
            $table->foreign('department_id')->references('id')
                ->on('departments')
                ->onDelete('cascade');
            $table->unsignedBigInteger('doctor_id');
            $table->foreign('doctor_id')->references('id')
                ->on('users')
                ->onDelete('cascade');
            $table->unsignedBigInteger('patient_id');
            $table->foreign('patient_id')->references('id')
                ->on('patients')
                ->onDelete('cascade');
            $table->unsignedBigInteger('diagnosis_id');
            $table->foreign('diagnosis_id')->references('id')
                ->on('diagnosis')
                ->onDelete('cascade');
            $table->unsignedBigInteger('created_by');
            $table->foreign('created_by')->references('id')
                    ->on('users')
                    ->onDelete('cascade');

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
        Schema::dropIfExists('appointments');
    }
}
