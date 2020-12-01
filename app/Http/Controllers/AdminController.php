<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Parameter;
use App\Presenters\ParameterPresenter;
use App\User;
use App\Presenters\UserPresenter;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use DB;

class AdminController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(User $user, UserPresenter $userPresenter, Parameter $parameter, ParameterPresenter $parameterPresenter)
    {
        $this->user = $user;
        $this->parameter = $parameter;
        $this->userPresenter = $userPresenter;
        $this->parameterPresenter = $parameterPresenter;
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        return view('admins');
    }

    public function getAdmins(Request $request)
    {
        $limit = $request->limit;
        $offset = $request->offset;
        $page = $request->page;
        $orderByDesc = $request->orderByDesc ? 'desc' : 'asc';
        $key = $request->key;

        $query = $this->user->select('users.*')
                            ->where(function($query) use ($key) {
                                $query->where('users.name', 'LIKE', '%'.$key.'%')
                                    ->orWhere('users.email', 'LIKE', '%'.$key.'%');
                            })
                            ->where('users.type', '!=' , 'administrator');

        switch ($request->orderby) {

            case 'name':
                $query->orderBy('users.name', $orderByDesc);
                break;

            case 'email':
                $query->orderBy('users.email', $orderByDesc);
                break;

        }

        $paginator = $query->paginate($limit, ['*'], 'page', $page);

        $result = $paginator->getCollection();
        $result = $this->userPresenter->transformCollection($result);
        $admins = [];
        foreach ($result as &$admin) {
            $params = $this->parameter->select('parameters.*')
                                    ->leftJoin('param_group', 'param_group.param_id', '=', 'parameters.id')
                                    ->where('param_group.user_id', $admin['id'])
                                    ->get();
            $params = $this->parameterPresenter->transformCollection($params)->toArray();
            $admin['parameters'] = implode(',', array_map( function($param) {
                    return $param['name'];
                }, $params
            ));
            array_push($admins, $admin);
        }

        $paginatorJson = json_decode($paginator->toJson());

        return response([
                    'count'         => $paginatorJson->total,
                    'offset'        => $offset,
                    'limit'         => $limit,
                    'orderBy'       => $request->orderby,
                    'orderByDesc'   => $request->orderByDesc,
                    'result'        => $admins
                ]);
    }

    public function addAdmin()
    {
        $result = $this->parameter->get();
        $parameterList = json_encode($this->parameterPresenter->transformCollection($result));
        return view('admin-create', compact('parameterList'));
    }

    public function createAdmin(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|min:3',
            'email' => 'required|email|max:255|unique:users',
            'password' => 'required|confirmed',
            'parameters' => 'required|string'
        ]);


        if ($validator->fails()) {
            return redirect()->back()
                        ->withErrors($validator)
                        ->withInput();
        }

        try {
            DB::beginTransaction();

            $id = $this->user->create([
                'name'              => $request->name,
                'email'             => $request->email,
                'password'          => Hash::make($request->password)
            ])->id;
            $params = explode(',', $request->parameters);
            foreach($params as $param) {
                DB::table('param_group')->insert(
                    ['param_id' => $param, 'user_id' => $id]
                );
            }

            DB::commit();
        }
        catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()
                            ->withErrors(['message' => 'There was a problem creating the administrator.'])
                            ->withInput();
        }

        return redirect('/admin');

    }

    public function editAdmin($id)
    {
        $result = $this->user->where('id', $id)->get();
        $param = $this->userPresenter->transformCollection($result)[0];

        $params = $this->parameter->select('parameters.*')
                                ->leftJoin('param_group', 'param_group.param_id', '=', 'parameters.id')
                                ->where('param_group.user_id', $id)
                                ->get();
        $params = $this->parameterPresenter->transformCollection($params)->toArray();
        $param['parameters'] = array_map( function($param) {
                return $param['id'];
            }, $params
        );
        $param = json_encode($param);

        $result = $this->parameter->get();
        $parameterList = json_encode($this->parameterPresenter->transformCollection($result));
        return view('admin-edit', compact('param','parameterList'));
    }

    public function updateAdmin(Request $request)
    {
        if(isset($request->id))
            $client = $this->user->where('id', $request->id)->first();

        $validator = Validator::make($request->all(), [
            'id'            => 'required',
            'name'          => 'required|min:3',
            'email'         => 'required|email|max:255',
            'password'      => 'required|confirmed',
            'parameters'    => 'required|string'
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
                'password'          => Hash::make($request->password)
            ]);
            DB::table('param_group')->where('user_id', $client->id)->delete();
            $params = explode(',', $request->parameters);
            foreach($params as $param) {
                DB::table('param_group')->insert(
                    ['param_id' => $param, 'user_id' => $client->id]
                );
            }

            DB::commit();
        }
        catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()
                            ->withErrors(['message' => 'There was a problem updating the administrator.'])
                            ->withInput();
        }

        return redirect('/admin');

    }

    public function deleteAdmin($id)
    {
        $this->user->where('id', $id)->delete();
        DB::table('param_group')->where('user_id', $id)->delete();
        return redirect('/admin');

    }
}
