<?php
namespace App\Presenters;

abstract class Presenter {

	public function transformCollection(\Illuminate\Database\Eloquent\Collection $items)
    {
    	return $items->map(function ($item) {
    		return $this->transform($item);
    	});
    }

    public abstract function transform($item);
}