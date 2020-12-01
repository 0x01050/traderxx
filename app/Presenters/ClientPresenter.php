<?php
namespace App\Presenters;
use Auth;

class ClientPresenter extends Presenter {

    public function transform($client)
    {
        $statusArray = ['Paused', 'Active'];

        return [
            'id'                    => $client->id,
            'name'                  => $client->name,
            'email'                 => $client->email,
            'license'               => $client->license,
            'api'                   => $client->api,
            'secret_key'            => $client->secret_key,
            'parameter'             => isset($client->parameter->name) ? $client->parameter->name : '',
            'parameter_id'          => $client->parameter_id,
            'expire_date'           => date('m/d/Y',strtotime($client->expire_date)),
            'balance'               => number_format($client->balance/100000000, 4),
            'balance_max'           => number_format($client->balance_max/100000000, 4),
            'balance_max_value'     => $client->balance_max,
            'remark'                => $client->remark,
            'status'                => $client->status,
            'status_string'         => $statusArray[$client->status],
            'created_by'            => $client->created_by
        ];
    }

}
