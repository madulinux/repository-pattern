<?php

namespace Tests\Stubs;

use Illuminate\Database\Eloquent\Model;

class TestModel extends Model
{
    protected $fillable = ['name'];
    protected $table = 'test_models';
}
