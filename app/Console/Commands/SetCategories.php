<?php

namespace App\Console\Commands;

use App\Category;
use App\Product;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class SetCategories extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'set:categories';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'insert new categories from unfound categories then delete thim';

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
        $products = Product::where('status', 'REGEXP', 'active')->where('code', NULL)->where('is_variant', false)->get()->all();
        $length = sizeof($products);

        $i = 0;
        foreach ($products as $product) {
            $tags = trim(preg_replace('/\"|\'|\-/', '', $product->tags));
            $items = explode(',', $tags);

            foreach ($items as $item) {
                // case here : parentCategory_childCategory
                if (preg_match('/\_/', $item)) {
                    $data = explode('_', $item);

                    $parent = trim($data[0]);
                    $child = trim($data[1]);

                    $parentCategory = Category::where('name', $parent)->get()->first();
                    $childCategory = Category::where('name', $child)->get()->first();

                    $lastId = $this->lastMainCategoryId();

                    if (!$parentCategory) {
                        $this->info('create new maub category - name: ' . $parent . ' , number: ' . (1 + $lastId));

                        $parent = Category::create([
                            'name' => $parent,
                            'category_number' => ++$lastId,
                            'type' => 'main'
                        ]);

                        $parent_category_number = $lastId;
                    } else {
                        $this->info($parent . ' category are exist');

                        $parent_category_number = $parentCategory->category_number;
                    }

                    $lastId = $this->lastSubCateogryIdForMain($parent_category_number);

                    if (!$childCategory) {
                        $this->info('create new sub category - name: ' . $child . ' , number: ' . (1 + $lastId));

                        $parent = Category::create([
                            'name' => $child,
                            'category_number' => ++$lastId,
                            'type' => 'sub'
                        ]);

                    } else {
                        $this->info($child . ' category are exist');
                    }

                    $i++;
                    $this->info('next ..');
                }
            }
        }

        $this->info($i . ' categories were created');

    }

    function lastMainCategoryId()
    {
        $cats = $this->getMainCategories();
        return (int)$cats[sizeof($cats) - 1];
    }

    function getMainCategories()
    {
        return Category::where('type', 'main')->get()->sortBy(function ($category) {
            return $category->category_integer_number;
        })->pluck('category_number');
    }

    function lastSubCateogryIdForMain($main_id)
    {
        $cats = $this->getSubCategoriesId($main_id);

        return sizeof($cats) ? (int)$cats[sizeof($cats) - 1] : (int)($main_id . "0");
    }

    function getSubCategoriesId($parent_id)
    {
        return Category::where('type', 'sub')->where('category_number', 'REGEXP', '^' . $parent_id)->get()->sortBy(function ($cat) {
            return $cat->category_integer_number;
        })->pluck('category_number');
    }

    function makeCategories($item)
    {
        $categories = explode('_', $item);

        $parent = trim($categories[0]);
        $child = trim($categories[1]);

        $category = Category::where('name', 'REGEXP', 'عطور')->first();

        if ($category)
            dd($category);
    }

    function ss()
    {
        /*
         *         // all products that active and it's product not variant
        // 2 parameters in this algo. are important
        // 1. product type
        // 2. product tags
        // search if the type isn't in Categories table
        // if isn't apply the algo.
        // the idea of algo. is check if the type is exist in tags
        // then determine parent and child, add to categories
        // type if is more than tirm
        // in both cases use regex
        // if type[0] in tags
        // or type[1] in tags
        // yes: go to step #2
        // split tags according (( , )) key
        // loop in all items as a result of split
        // each item apply regex
        // if the type or type[0] or type[1] in item
        // and if there is (( _ )) key
        // then result[0] is the parent
        // result[1] is the child
        // add them in categories

        foreach ($products as $product) {
            $this->info('new category if is not exist .. tags for product id : ' . $product->id);

            $tags = trim(preg_replace('/\"|\'|\-/', '', $product->tags));

            $arr = explode(' ', $product->type);
            if (!Category::where('name', $product->type)->first() && sizeof($arr) > 1) {
                dd($tags);

                if (preg_match('/' . trim($arr[0]) . '| ' . trim($arr[1]) . ' /', $tags)) {

                    $arr = explode(',', $tags);
                    dd($tags);

                    foreach ($arr as $item) {

                        if (preg_match('/\_/', $item)) {

                            $item = trim(preg_replace('/\"|\,|\'|\-/', '', $item));

                            foreach (explode(' ', $item) as $part) {

                                if (preg_match('/' . $part . '/', $product->type, $result1) || preg_match('/' . $product->type . '/', $part, $result2)) {
                                    if ($result1) {
                                        dd($result1[0]);
                                        if (!Category::where('name', 'REGEXP', $result1[0])->first())
                                            $this->makeCategories($item);
                                    } else if ($result2) {

                                    }

                                }
                            }

                        }

                    }
                }
            }


        }

         */
        $keyword = ['نسائي', 'نسائيه', 'رجاليه', 'رجالي'];

        $u_f_cs = DB::table('unfound_categories')->get()->all();
        $tags = Product::all()->pluck('tags');

        foreach ($u_f_cs as $u_f_c) {

            foreach ($tags as $tag) {
                if (preg_match('/_' . $u_f_c->name . '/', $tag)) {
                    $arr = explode(',', $tag);

                    foreach ($arr as $item) {
                        if (preg_match('/(.+)_' . $u_f_c->name . '/', $item, $filter)) {
                            $result = trim($filter[1]);
                            // here some impl..
                        }
                    }

                    dd($arr);
                }
            }
        }

        $categories = Category::all()->pluck('name');
        $unFoundCategories = DB::table('unfound_categories')->get()->all();

        foreach ($unFoundCategories as $category) {
            $this->info(/*'try to find print of this category' . */ $category->name);

            $tags = Product::all()->where('type', $category->name)->pluck('tags');
            $this->info('number of product use this tag' . sizeof($tags));
            // 1th unfound category
            // get all tags for products have type as unfound category
            // make your iteration in tags and then search if there is '_' in each tag
            // if yes ! split by '_'
            // check if one of two term is in categories

            foreach ($tags as $tag) {
                $arr = explode(',', $tag);

                foreach ($arr as $item) {
                    if (preg_match('/_/', $item)) {
                        $sub = explode('_', $item);

                        if (in_array($category->name, $sub)) {
                            $this->info('searching');
                            $parent = str_replace(",", "", trim($sub[0]));

                            if (in_array($parent, $categories->toArray())) {
                                $this->info('more searching ..');
                                // there are data for his parent
                                $parent = Category::where('name', $parent)->first();

                                $cats = Category::where('category_number', 'REGEXP', '^' . $parent->category_number . '')
                                    ->get()->pluck('category_number');

                                $id = $parent->category_number . sizeof($cats);
                                if (!Category::where('category_number', $id)->first()) {
                                    $this->info('add new category : ' . $category->name . ' for existing parent :' . $parent->name);
                                    Category::create([
                                        'name' => $category->name,
                                        'category_number' => $id,
                                    ]);

                                    $this->info('deleted from unfound categories');
                                    DB::table('unfound_categories')->delete($category->id);
                                    break;
                                }
                            } else {

                            }
                        }
                    } else {

                    }
                }
            }
            $this->info('**END');
        }

    }
}
