<?php

use Illuminate\Database\Seeder;

class CategoriesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $main = $this->get_main_categories();
        $i = 0;
        foreach ($main['categories'] as $item)
            \App\Category::create(
                [
                    'name' => $item,
                    'category_number' => $main['numbers'][$i++],
                ]
            );

        $sub = $this->get_sub_categories();
        $i = 0;
        foreach ($sub['categories'] as $item)
            \App\Category::create(
                [
                    'name' => $item,
                    'category_number' => $sub['numbers'][$i++],
                    'type' => 'sub',
                ]
            );

    }

    public function get_main_categories()
    {

        $categories = file_get_contents(__DIR__ . '/main_categories.txt');
        $numbers = file_get_contents(__DIR__ . '/numbers.txt');

        preg_match_all('/(.+?)\n/', $categories, $main_categories);
        preg_match_all('/(.+?)\n/', $numbers, $numbers);

        return ['categories' => $main_categories[1], 'numbers' => $numbers[1]];
    }

    public function get_sub_categories()
    {
        $categories = file_get_contents(__DIR__ . '/sub_categories.txt');
        $numbers = file_get_contents(__DIR__ . '/sub_numbers.txt');

        preg_match_all('/(.+?)\n/', $categories, $main_categories);
        preg_match_all('/(.+?)\n/', $numbers, $numbers);

        return ['categories' => $main_categories[1], 'numbers' => $numbers[1]];
    }

}
