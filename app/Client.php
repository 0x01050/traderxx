<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
    //
    protected $guarded = [];

    public function parameter()
    {
        return $this->hasOne('App\Parameter', 'id', 'parameter_id');
    }
}
