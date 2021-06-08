<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $guarded = ['id'];

    protected $appends = ['category_integer_number'];

    public function getCategoryIntegerNumberAttribute()
    {
        return (int)$this->category_number;
    }
}
