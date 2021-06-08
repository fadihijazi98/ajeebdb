<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call(ProductsTableSeeder::class);
        $this->call(CategoriesSeeder::class);
        $this->findUnfoundCategories();
    }

    public function findUnfoundCategories()
    {
        $types = (\App\Product::all()->where('is_variant', false))->pluck('type');
        $categories = \App\Category::all()->pluck('name');

        foreach ($types as $type) {
            $u_c = \Illuminate\Support\Facades\DB::table('unfound_categories');
            $this->command->info('search in this type ' . $type);

            if (!in_array($type, $categories->toArray()) && !$u_c->where('name', $type)->first())
                \Illuminate\Support\Facades\DB::table('unfound_categories')->insert([
                    'name' => $type
                ]);

        }
    }
}
