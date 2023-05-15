<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Actividad;
use App\Models\PerfilPuesto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index()
    {
        $cond=[ 'is_deleted' => 0];
         $usuarios = User::with('rol','puesto.area')->where($cond)->orderBy('name')->get()->toArray();

        return ($usuarios);

    }
    public function getUser()
    {
        $cond=[ 'id' => auth()->user()->id];
         $usuario = User::with('rol','puesto.area')->where($cond)->first();

        return ($usuario);

    }
    public function getUserActivity($id)
    {
        $actividad = Actividad::find($id);
        $cond=[ 'id' => $actividad->usuario_id];
         $usuario = User::with('rol','puesto.area')->where($cond)->first();

        return ($usuario);

    }
    public function indexAll() //reportes
    {
        $cond=[
            ['is_deleted','==', 0],
            ];
            $role=[];
        $cargo=[];
        if (auth()->user()->role==2) { //ADMINISTRADOR
            $cond = [];
        } elseif (auth()->user()->role==3) { //SUPERVISOR
            $role = [1,2,3];
            $cargo = [1,2,3,4,5,6,7,8,9,10,11,12];

            if ((auth()->user()->cargo==9) || (auth()->user()->cargo==10)) { // coordinador CERT
                $role = [1,3];
                $cargo = [1,2,3,4,5,6,7,8,9,10,11];
            } elseif (auth()->user()->cargo==4) { // coordinador del CERT
                $role = [1,3];
                $cargo = [1,3,4];
            } elseif (auth()->user()->cargo==8) { //coordinador del infraestructura
                $role = [1,3];
                $cargo = [7,8,13];
            } elseif (auth()->user()->cargo==6) { //coordinador del infraestructura
                $role = [1,3];
                $cargo = [5,6];
            }
        }

        $usuarios = User::with('rol','puesto.area')->where($cond)->whereIn('role',$role)->whereIn('cargo',$cargo)->whereNotIn('id',[19,50])->orderBy('name')->get()->toArray();

        return ($usuarios);

    }
    public function indexByArea($area) //reportes
    {
        $cond=[ 'area_id' => $area];

        $perfiles = PerfilPuesto::where($cond)->get()->toArray();
        $arrayPerfiles=[];

        foreach ($perfiles as $item) {
            array_push($arrayPerfiles, $item['id']);
        }
            //dd($perfil['id']);
            $cond=[
            ['is_deleted','==', 0],
            ];

            $usuarios = User::with('rol','puesto.area')->where($cond)->whereIn('cargo',$arrayPerfiles)->whereNotIn('id',[19,50])->orderBy('name')->get()->toArray();


        return($usuarios);

    }
    public function store(Request $request)
    {
        // $client = Cliente::create($request->all());
        // $clientes = Cliente::all();
        // return view('cliente.index')->with('clientes', $clientes);

        $user = new User([
            'name' => $request->input('name'),
            'email' => $request->input('email'),
            'role' => $request->input('role'),
            'cargo' => $request->input('cargo'),
            'password' => Hash::make($request->input('password')),
        ]);
        $user->save();

        return response()->json('user created!');

    }
    public function usuarioUpdatePassword(Request $request)
    {

        $user = User::where('id',$request->user)->first();
        if (Hash::check( $request->old_password, $user->password)) {
            $user->password = Hash::make($request->new_password);
            $user->save();
            # code...
            return response()->json('user created!');
        }else{
            return response()->json('password error!');

        }


    }
    public function show($id)
    {
        $user = User::with('rol','puesto.area')->find($id);
        return response()->json($user);
    }

    public function update($id, Request $request)
    {
        $user = User::find($id);
        $user->name = $request->input('name');
        $user->email = $request->input('email');
        $user->role = $request->input('role');
        $user->cargo = $request->input('cargo');
        $user->save();

        return response()->json('user updated!');
    }

    public function destroy($id)
    {
        $user = User::find($id);
        //$user->delete();
        $user->is_deleted=1;
        $user->save();
        return response()->json('user deleted!');
    }
}
