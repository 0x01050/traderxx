<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Client;
use App\Presenters\ClientPresenter;
use App\Parameter;
use App\Presenters\ParameterPresenter;
use Illuminate\Support\Facades\Validator;
use DB;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(Client $client, ClientPresenter $clientPresenter, Parameter $parameter, ParameterPresenter $parameterPresenter)
    {
        $this->client = $client;
        $this->clientPresenter = $clientPresenter;
        $this->parameter = $parameter;
        $this->parameterPresenter = $parameterPresenter;
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        return view('clients');
    }

    public function getClients(Request $request)
    {
        $limit = $request->limit;
        $offset = $request->offset;
        $page = $request->page;
        $orderByDesc = $request->orderByDesc ? 'desc' : 'asc';
        $key = $request->key;

        $query = $this->client->select('clients.*')
                            ->selectSub('parameters.name', 'parameterName')
                            ->leftJoin('parameters', 'clients.parameter_id', '=', 'parameters.id')
                            ->where('clients.name', 'LIKE', '%'.$key.'%')
                            ->orWhere('email', 'LIKE', '%'.$key.'%')
                            ->orWhere('license', 'LIKE', '%'.$key.'%')
                            ->orWhere('api', 'LIKE', '%'.$key.'%')
                            ->orWhere('secret_key', 'LIKE', '%'.$key.'%')
                            ->orWhere('parameters.name', 'LIKE', '%'.$key.'%')
                            ->orWhere('remark', 'LIKE', '%'.$key.'%');

        switch ($request->orderby) {

            case 'name':
                $query->orderBy('name', $orderByDesc);
                break;

            case 'email':
                $query->orderBy('email', $orderByDesc);
                break;

            case 'license':
                $query->orderBy('license', $orderByDesc);
                break;

            case 'api':
                $query->orderBy('api', $orderByDesc);
                break;

            case 'secret_key':
                $query->orderBy('secret_key', $orderByDesc);
                break;

            case 'parameter':
                $query->orderBy('parameterName', $orderByDesc);
                break;

            case 'expire_date':
                $query->orderBy('expire_date', $orderByDesc);
                break;

            case 'balance':
                $query->orderBy('balance', $orderByDesc);
                break;

            case 'status':
                $query->orderBy('status', $orderByDesc);
                break;
            
        }

        $paginator = $query->paginate($limit, ['*'], 'page', $page);

        $result = $paginator->getCollection();
        $paginatorJson = json_decode($paginator->toJson());

        return response([
                    'count'         => $paginatorJson->total, 
                    'offset'        => $offset,
                    'limit'         => $limit,
                    'orderBy'       => $request->orderby,
                    'orderByDesc'   => $request->orderByDesc,
                    'result'        => $this->clientPresenter->transformCollection($result)
                ]);
    }

    public function addClient()
    {
        $result = $this->parameter->get();
        $parameterList = json_encode($this->parameterPresenter->transformCollection($result));
        return view('client-create', compact('parameterList'));
    }

    public function createClient(Request $request) 
    { 
        $validator = Validator::make($request->all(), [
            'name' => 'required|min:3',
            'email' => 'required|email|max:255',
            'api' => 'required|unique:clients',
            'secret_key' => 'required|unique:clients',
            'license' => 'required|unique:clients',
            'expire_date' => 'required|date',
            'parameter' => 'required|integer',
            'balance_max' => 'required|integer',
            'status' => 'required|integer',
        ]);


        if ($validator->fails()) {
            return redirect()->back()
                        ->withErrors($validator)
                        ->withInput();
        }

        try {
            DB::beginTransaction();

            $this->client->create([
                'name'              => $request->name,
                'email'             => $request->email, 
                'api'               => $request->api,
                'secret_key'        => $request->secret_key,
                'license'           => $request->license,            
                'expire_date'       => date('Y-m-d', strtotime($request->expire_date)),
                'parameter_id'      => $request->parameter,
                'balance_max'       => $request->balance_max,
                'status'            => $request->status,
                'remark'            => $request->remark
            ]);
            
            DB::commit();
        }
        catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()
                            ->withErrors(['message' => 'There was a problem creating the user.'])
                            ->withInput();
        }

        return redirect('/home');

    }

    public function editClient($id)
    {
        $result = $this->client->where('id', $id)->get();
        $param = json_encode($this->clientPresenter->transformCollection($result)[0]);

        $result = $this->parameter->get();
        $parameterList = json_encode($this->parameterPresenter->transformCollection($result));
        return view('client-edit', compact('param','parameterList'));
    }

    public function updateClient(Request $request) 
    { 
        if(isset($request->id))
            $client = $this->client->where('id', $request->id)->first();

        $validator = Validator::make($request->all(), [
            'id'            => 'required',
            'name'          => 'required|min:3',
            'email'         => 'required|email|max:255',
            'api'           => (isset($client->api) && isset($request->api) && $client->api == $request->api) ? 'required' : 'required|unique:clients',
            'secret_key'    => (isset($client->secret_key) && isset($request->secret_key) && $client->secret_key == $request->secret_key) ? 'required' : 'required|unique:clients',
            'license'       => (isset($client->license) && isset($request->license) && $client->license == $request->license) ? 'required' : 'required|unique:clients',
            'expire_date'   => 'required|date',
            'parameter'     => 'required|integer',
            'balance_max'   => 'required|integer',
            'status' => 'required|integer',
        ]);


        if ($validator->fails()) {
            return redirect()->back()
                        ->withErrors($validator)
                        ->withInput();
        }

        try {
            DB::beginTransaction();

            $client->update([
                'name'              => $request->name,
                'email'             => $request->email, 
                'api'               => $request->api,
                'secret_key'        => $request->secret_key,
                'license'           => $request->license,            
                'expire_date'       => date('Y-m-d', strtotime($request->expire_date)),
                'parameter_id'      => $request->parameter,
                'balance_max'       => $request->balance_max,
                'status'            => $request->status,
                'remark'            => $request->remark
            ]);
            
            DB::commit();
        }
        catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()
                            ->withErrors(['message' => 'There was a problem updating the user.'])
                            ->withInput();
        }

        return redirect('/home');

    }

    public function deleteClient($id) 
    { 
        $result = $this->client->where('id', $id)->delete();
        return redirect('/home');

    }

    public function getStatusInfo()
    {
        $result = $this->parameter->get();
        return response($this->parameterPresenter->statusInfoCollection($result));
    }
}
