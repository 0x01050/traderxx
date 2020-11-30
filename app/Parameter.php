<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Parameter extends Model
{
    //
    protected $guarded = [];

    public function clients()
    {
        return $this->hasMany('App\Client', 'parameter_id', 'id');
    }
}
