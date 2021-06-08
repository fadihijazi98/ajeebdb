<?php

use Illuminate\Database\Seeder;

class ProductsTableSeeder extends Seeder
{
    public function run()
    {
        $file = fopen(__DIR__ . '/products.csv', 'r');
        $i = 0;
        $headers = [];
        $values = [];
        $sucess_counter = 0;
        $failed_counter = 0;

        while (!feof($file))
            if ($i++ == 0) {
                $headers = explode(',', fgets($file));
                $headers[sizeof($headers) - 1] = 'status';
            } else {
                $values = $this->escape_tags_case_when_split(fgets($file));

                if (sizeof($values) == 29) {
                    $sucess_counter++;
                    $product = \App\Product::create(array_combine($headers, $values));
                    $this->command->info('the product ' . $product->handle . ' has been imported');
                } else {
                    $failed_counter++;
                    $this->command->error(fgets($file));

                    $product = \App\Product::create([
                        'handle' => (explode(',', fgets($file)))[0]
                    ]);

                    \Illuminate\Support\Facades\DB::table('failed_fields')->insert([
                        'line_content' => fgets($file),
                        'product_id' => $product->id,
                    ]);
                }
            }

        $this->command->info('products success: ' . $sucess_counter . ', products failed: ' . $failed_counter);

        fclose($file);
    }

    function escape_tags_case_when_split($item)
    {
        return preg_split("/,(?=([^\"]*\"[^\"]*\")*[^\"]*$)/", $item);
    }
}
