<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Parameter;
use App\Presenters\ParameterPresenter;
use Illuminate\Support\Facades\Validator;
use DB;

class ParameterController extends Controller
{
    //
    public function __construct(Parameter $parameter, ParameterPresenter $parameterPresenter)
    {
        $this->parameter = $parameter;
        $this->parameterPresenter = $parameterPresenter;
    }

    public function index()
    {
        return view('parameters');
    }

    public function getParameters(Request $request)
    {
        $limit = $request->limit;
        $offset = $request->offset;
        $page = $request->page;
        $orderByDesc = $request->orderByDesc ? 'desc' : 'asc';
        $key = $request->key;

        $query = $this->parameter->where('name', 'LIKE', '%'.$key.'%')
        						->orWhere('connection_interval', $key)
        						->orWhere('vol_min', $key)
        						->orWhere('vol_bin_size', 'LIKE', '%'.$key.'%')
        						->orWhere('vol_count', $key)
        						->orWhere('limit_profit', $key)
        						->orWhere('order_count', $key)
        						->orWhere('order_distance', $key)
        						->orWhere('qty_rate', $key)
        						->orWhere('stop_loss', $key);

        switch ($request->orderby) {

            case 'name':
                $query->orderBy('name', $orderByDesc);
                break;

            case 'connection_interval':
                $query->orderBy('connection_interval', $orderByDesc);
                break;

            case 'vol_min':
                $query->orderBy('vol_min', $orderByDesc);
                break;

            case 'vol_bin_size':
                $query->orderBy('vol_bin_size', $orderByDesc);
                break;

            case 'vol_count':
                $query->orderBy('vol_count', $orderByDesc);
                break;

            case 'limit_profit':
                $query->orderBy('limit_profit', $orderByDesc);
                break;

            case 'order_count':
                $query->orderBy('order_count', $orderByDesc);
                break;

            case 'order_distance':
                $query->orderBy('order_distance', $orderByDesc);
                break;

            case 'qty_rate':
                $query->orderBy('qty_rate', $orderByDesc);
                break;

            case 'stop_loss':
                $query->orderBy('stop_loss', $orderByDesc);
                break;

            case 'force_stop':
                $query->orderBy('force_stop', $orderByDesc);
                break;

            case 'martingale':
                $query->orderBy('martingale', $orderByDesc);
                break;

            case 'inverse':
                $query->orderBy('inverse', $orderByDesc);
                break;

            case 'direction':
                $query->orderBy('direction', $orderByDesc);
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
                    'result'        => $this->parameterPresenter->transformCollection($result)
                ]);
    }

    public function addParameter()
    {
        return view('parameter-create');
    }

    public function createParameter(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name'          				=> 'required',
            'connection_interval'         	=> 'required|min:5|integer',
            'vol_min'         				=> 'required|min:1|integer',
            'vol_bin_size'         			=> 'required',
            'vol_count'         			=> 'required|min:1|max:1000|integer',
            'limit_profit'         			=> 'required|min:1|integer',
            'order_count'         			=> 'required|min:0|integer',
            'order_distance'         		=> 'required|min:1|integer',
            'qty_rate'         				=> 'required|max:1|numeric',
            'stop_loss'         			=> 'required|min:0|integer',
            'force_stop'                    => 'required|integer',
            'martingale'                    => 'required|numeric',
            'inverse'                       => 'required|integer',
            'direction'                     => 'required|integer'
        ]);


        if ($validator->fails()) {
            return redirect()->back()
                        ->withErrors($validator)
                        ->withInput();
        }

        try {
            DB::beginTransaction();

            $this->parameter->create([
                'name'              				=> $request->name,
                'connection_interval'             	=> $request->connection_interval,
                'vol_min'               			=> $request->vol_min,
                'vol_bin_size'        				=> $request->vol_bin_size,
                'vol_count'           				=> $request->vol_count,
                'limit_profit'       				=> $request->limit_profit,
                'order_count'      					=> $request->order_count,
                'order_distance'       				=> $request->order_distance,
                'qty_rate'            				=> $request->qty_rate,
                'stop_loss'            				=> $request->stop_loss,
                'force_stop'                        => $request->force_stop,
                'martingale'                        => $request->martingale,
                'inverse'                           => $request->inverse,
                'direction'                         => $request->direction
            ]);

            DB::commit();
        }
        catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()
                            ->withErrors(['message' => 'There was a problem creating the parameter.'])
                            ->withInput();
        }

        return redirect('/parameter');

    }

    public function editParameter($id)
    {
        $result = $this->parameter->where('id', $id)->get();
        $param = json_encode($this->parameterPresenter->transformCollection($result)[0]);
        return view('parameter-edit', compact('param'));
    }

    public function updateParameter(Request $request)
    {
        if(isset($request->id))
            $parameter = $this->parameter->where('id', $request->id)->first();

        $validator = Validator::make($request->all(), [
            'id'            				=> 'required',
            'name'          				=> 'required',
            'connection_interval'         	=> 'required|min:5|integer',
            'vol_min'         				=> 'required|min:1|integer',
            'vol_bin_size'         			=> 'required',
            'vol_count'         			=> 'required|min:1|max:1000|integer',
            'limit_profit'         			=> 'required|min:1|integer',
            'order_count'         			=> 'required|min:0|integer',
            'order_distance'         		=> 'required|min:1|integer',
            'qty_rate'         				=> 'required|max:1|numeric',
            'stop_loss'         			=> 'required|min:0|integer',
            'force_stop'                    => 'required|integer',
            'martingale'                    => 'required|numeric',
            'inverse'                       => 'required|integer',
            'direction'                     => 'required|integer'
        ]);


        if ($validator->fails()) {
            return redirect()->back()
                        ->withErrors($validator)
                        ->withInput();
        }

        try {
            DB::beginTransaction();

            $parameter->update([
                'name'              				=> $request->name,
                'connection_interval'             	=> $request->connection_interval,
                'vol_min'               			=> $request->vol_min,
                'vol_bin_size'        				=> $request->vol_bin_size,
                'vol_count'           				=> $request->vol_count,
                'limit_profit'       				=> $request->limit_profit,
                'order_count'      					=> $request->order_count,
                'order_distance'       				=> $request->order_distance,
                'qty_rate'            				=> $request->qty_rate,
                'stop_loss'            				=> $request->stop_loss,
                'force_stop'                        => $request->force_stop,
                'martingale'                        => $request->martingale,
                'inverse'                           => $request->inverse,
                'direction'                         => $request->direction
            ]);

            DB::commit();
        }
        catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()
                            ->withErrors(['message' => 'There was a problem updating the parameter.'])
                            ->withInput();
        }

        return redirect('/parameter');

    }

    public function deleteParameter($id)
    {
        $result = $this->parameter->where('id', $id)->delete();
        DB::table('param_group')->where('param_id', $id)->delete();
        return redirect('/parameter');

    }
}
