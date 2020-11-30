<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Contracts\Encryption\DecryptException;
use App\Client;
use App\Parameter;

class ApiController extends Controller
{
    //
    public function __construct(Client $client, Parameter $parameter)
    {
        $this->client = $client;
        $this->parameter = $parameter;
    }

    public function ping(Request $request)
    {
    	$enc_message = $request->q;

    	$dec_message;
    	$message;
    	try {
	    	$dec_message = Crypt::decryptString($enc_message);
	    	$message = json_decode($dec_message);
    	} catch (DecryptException $e) {
		    //
    		$result = [
	    		'success' => false,
	    		'message' => 'unknown error.'
	    	];

	    	$enc_result = Crypt::encryptString(json_encode($result));
    		echo $enc_result;

    		return;
		}		

		if(!isset($message->email))
		{
			$result = [
	    		'success' => false,
	    		'message' => 'The email is required.'
	    	];

	    	$enc_result = Crypt::encryptString(json_encode($result));
    		echo $enc_result;

    		return;
		}

		$email = $message->email;

		if(!isset($message->license))
		{
			$result = [
	    		'success' => false,
	    		'message' => 'The license is required.'
	    	];

	    	$enc_result = Crypt::encryptString(json_encode($result));
    		echo $enc_result;

    		return;
		}

		$license = $message->license;

		$client = $this->client->where('email', $email)->where('license', $license)->first();

		if(!isset($client))
    	{
    		$result = [
	    		'success' => false,
	    		'message' => 'The email or license is invalid.'
	    	];

	    	$enc_result = Crypt::encryptString(json_encode($result));
    		echo $enc_result;

    		return;
    	}

    	$expire_date = $client->expire_date;
    	$diff = date_diff(date_create(date('Y-m-d')), date_create($expire_date));
    	$remaining_days = intval($diff->format('%R') == '+' ? $diff->format('%a') : '-'.$diff->format('%a'));
    	if(strtotime(date('Y-m-d')) > strtotime($expire_date))
    	{
    		$result = [
	    		'success' 			=> false,
	    		'message' 			=> 'Your license has been expired.',
	    		'expire_date'		=> $expire_date,
	    		'remaining_days'	=> $remaining_days
	    	];

	    	$enc_result = Crypt::encryptString(json_encode($result));
    		echo $enc_result;

    		return;
    	}

    	if($client->status == 0)
    	{
    		$result = [
	    		'success' 			=> false,
	    		'message' 			=> 'Your license has been paused.',
	    		'expire_date'		=> $expire_date,
	    		'remaining_days'	=> $remaining_days
	    	];

	    	$enc_result = Crypt::encryptString(json_encode($result));
    		echo $enc_result;

    		return;
    	}

    	$parameter = $this->parameter->where('id', $client->parameter_id)->first();

    	if(!isset($parameter))
    	{
    		$result = [
	    		'success' 			=> false,
	    		'message' 			=> 'Your license is pending.',
	    		'expire_date'		=> $expire_date,
	    		'remaining_days'	=> $remaining_days
	    	];

	    	$enc_result = Crypt::encryptString(json_encode($result));
    		echo $enc_result;

    		return;
    	}

    	$client->last_message = $dec_message;
    	$client->save();

    	$result = [
    		'success' 			=> true,
    		'expire_date'		=> $expire_date,
    		'remaining_days'	=> $remaining_days,
    		'api'				=> $client->api,
    		'secret_key'		=> $client->secret_key,
    		'parameter'			=> [
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
    		]	
    	];

    	$enc_result = Crypt::encryptString(json_encode($result));
    	echo $enc_result;
    }
}
