<?php
namespace App\Presenters;
use Auth;

class ParameterPresenter extends Presenter {

    public function transform($parameter)
    {
        return [
            'id'                            => $parameter->id,
            'name'                          => $parameter->name, 
            'connection_interval'           => $parameter->connection_interval,
            'vol_min'                       => $parameter->vol_min,
            'vol_bin_size'                  => $parameter->vol_bin_size,
            'vol_count'                     => $parameter->vol_count,
            'limit_profit'                  => $parameter->limit_profit,
            'order_count'                   => $parameter->order_count,
            'order_distance'                => $parameter->order_distance,
            'qty_rate'                      => $parameter->qty_rate,
            'stop_loss'                     => $parameter->stop_loss
        ];
    }

    public function statusInfoCollection(\Illuminate\Database\Eloquent\Collection $parameters)
    {
        return $parameters->map(function ($item) {
            return [
                'name'            => $item->name,
                'total_count'     => count($item->clients),
                'active_count'    => count($item->clients->where('expire_date', '>', date('Y-m-d'))),
                'total_balance'   => number_format($item->clients->sum('balance')/100000000, 4)
            ];
        });
    }

}