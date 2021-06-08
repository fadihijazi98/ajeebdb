<?php

namespace App\Console\Commands;

use App\Product;
use Illuminate\Console\Command;

class SetVariants extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'set:variants';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'To distinguish between products and variants';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $products = Product::all();

        if (/*$this->argument('just_tags')*/ false)
            $this->setTags($products);
        else
            $this->setVariant($products);
    }

    function setVariant($products)
    {
        $last_product_name = "";
        $last_product_type = "";
        $last_product_tags = "";
        $last_product_status = "";

        foreach ($products as $product) {

            if ($product->type != null) {
                $this->info('product: ' . $product->handle . ' as product');

                $last_product_name = $product->handle;
                $last_product_type = $product->type;
                $last_product_tags = $product->tags;
                $last_product_status = $product->status;

                $product->is_variant = false;
                $product->save();
            } else if ($product->handle == $last_product_name) {
                $this->info('set product ' . $product->handle . ' as variant');
                $product->type = $last_product_type;
                $product->tags = $last_product_tags;
                $product->status = $last_product_status;

                $product->is_variant = true;

                $product->save();
            }

        }
    }

    function setTags($products)
    {
        $last_product_name = "";
        $last_product_type = "";
        $last_product_tags = "";

        foreach ($products as $product) {
            if ($product->handle == $last_product_name) {
                $this->info('update product ' . $product->handle . ' tags');
                $product->tags = $last_product_tags;

                $product->save();
            }

            $last_product_name = $product->handle;
            $last_product_type = $product->type;
            $last_product_tags = $product->tags;
        }
    }

}
