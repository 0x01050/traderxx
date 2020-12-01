<?php
namespace App\Presenters;

class UserPresenter extends Presenter {

    public function transform($client)
    {
        return [
            'id'                    => $client->id,
            'name'                  => $client->name,
            'email'                 => $client->email
        ];
    }

}
