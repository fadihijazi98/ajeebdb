<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();

            $table->string('handle')->nullable();
            $table->string('title')->nullable();
            $table->string('vendor')->nullable();
            $table->string('type')->nullable();
            $table->string('tags', 1000)->nullable();
            $table->string('published')->nullable();

            $table->string('option1_name')->nullable();
            $table->string('option1_value')->nullable();
            $table->string('option2_name')->nullable();
            $table->string('option2_value')->nullable();;
            $table->string('option3_name')->nullable();
            $table->string('option3_value')->nullable();

            $table->string('variant_sky')->nullable();
            $table->string('variant_grams')->nullable();
            $table->string('variant_inventory_track')->nullable();
            $table->string('variant_inventory_policy')->nullable();
            $table->string('variant_fulfillment_service')->nullable();
            $table->string('variant_price')->nullable();
            $table->string('variant_compare_at_price')->nullable();
            $table->string('variant_require_Shipping')->nullable();
            $table->string('variant_taxable')->nullable();
            $table->string('variant_barcode')->nullable();
            $table->string('image_src')->nullable();
            $table->string('image_position')->nullable();
            $table->string('image_alt')->nullable();
            $table->string('gift_card')->nullable();
            $table->string('variant_image')->nullable();
            $table->string('variant_weight')->nullable();
            $table->string('cost_per_item')->nullable();
            $table->string('status')->nullable();

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
        Schema::dropIfExists('products');
    }
}
