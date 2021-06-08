<?php

namespace App\Console\Commands;

use App\Category;
use App\Product;
use Illuminate\Console\Command;
use phpDocumentor\Reflection\Type;

class GenerateCode extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'generate:codes';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'make new code for each product xxx xxxxxx';

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

        /* Test
        $codes = Product::where('code', 'REGEXP', '^\d\d(?!0)')->get()->pluck('code')->toArray();
//        dd(sizeof(Product::where('status', 'REGEXP', 'active')->get()));
        array_push($codes, '101000785');

        //dd($codes);
        dd(array_unique($codes));
        /*cl*/

        $p_a = [];
        $counter = 0;

        foreach (Category::all() as $category) {
            $i = 1;

            $products0 = Product::where('status', 'REGEXP', 'active')->where('type', 'REGEXP', $category->name)->get();

            $this->info('fetch ' . sizeof($products0) . ' categories');
            array_push($p_a, ...($products0->pluck('id'))->toArray());

            foreach ($products0 as $product) {

                //first id in some category : 101000001 : 9 digit
                $idLength = 9;

                $category->category_number = $this->findCategoryByTag($product->type, $category->category_number, $product->tags) ?: $category->category_number;

                $categoryDigits = strlen($category->category_number);
                $iDigits = strlen("" . $i);

                $code = $category->category_number;
                for ($k = 0; $k < $idLength - ($categoryDigits + $iDigits); $k++)
                    $code .= "0";
                $code .= $i++;

                $product->code = $code;
                $product->save();

                $counter++;
                $this->info($i . 'th product, handle: ' . $product->handle . ' || ' . $code . ' has been updated');
            }

            $this->info($counter . " products are updated");

            $regex = $category->name . "|" . substr($category->name, 2);
            foreach (Product::where('status', 'REGEXP', 'active')->where('tags', 'REGEXP', $regex)->get() as $product) {

                if (in_array($product->id, $p_a)) {
                    $this->info('skip ..');
                } else {

                    preg_match('/' . $regex . '/', $product->tags, $result);

                    //first id in some category : 101000001 : 9 digit
                    $idLength = 9;

                    $category->category_number = $this->findCategoryByTag($result[0], $category->category_number, $product->tags) ?: $category->category_number;

                    $categoryDigits = strlen($category->category_number);
                    $iDigits = strlen("" . $i);

                    $code = $category->category_number;
                    for ($k = 0; $k < $idLength - ($categoryDigits + $iDigits); $k++)
                        $code .= "0";
                    $code .= $i++;

                    $product->code = $code;
                    $product->save();

                    $counter++;
                    $this->info($i . 'th product, handle: ' . $product->handle . ' || ' . $code . ' has been updated');

                    array_push($p_a, $product->id);
                }
            }

        }


        $this->info($counter . " products are updated");

    }

    public
    function findCategoryByTag($categoryName, $categoryNumber, $tags)
    {
        $category = Category::where('name', 'REGEXP', $categoryName)->first();

        if ($category && $category->type == 'main') {
            if ($category->type == 'main') {
                $arr = explode(',', $tags);

                $i = 0;
                foreach ($arr as $item) {
                    $item = trim(preg_replace('/\"|\,|\'|\-/', '', $item));

                    /*if ($i++ == 4)
                        dd($item);*/

                    // $item = العطور رجاليه
                    // $category->name = عطور

                    foreach (explode(' ', $item) as $part) {
                        if (preg_match('/' . preg_replace('/\//', '\/', $part) . '/', $categoryName, $result1) || preg_match('/' . $categoryName . '/', $part, $result2)) {
                            if ($result1 && (($newCat = Category::where('name', 'REGEXP', $item)->first()) ? $newCat->type == 'sub' : false))
                                return $newCat->category_number;
                            else if ($result2 && (($newCat = Category::where('name', 'REGEXP', $item)->first()) ? $newCat->type == 'sub' : false))
                                return $newCat->category_number;
                        }
                    }

                }

            } else
                return $category->category_number;
        } else {

            return $categoryNumber;
        }

    }
}
