<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\UserModel;

class UserController extends Controller
{
    public function getUser(){
        $list_user = UserModel::get();
        return response()->json($list_user);
    }

    public function registerUser(Request $req){
        $validate = Validator::make($req->all(),[
            'nama_user'=>'required|string|max:255',
            'role'=>'required',
            'username'=>'required|string|email|max:255|unique:user',
            'password'=>'required'
        ]);
        if($validate->fails()){
            return response()->json($validate->errors()->toJson, 400);
        }
        $register = UserModel::create([
            'nama_user'=>$req->nama_user,
            'role'=>$req->role,
            'username'=>$req->username,
            'password'=>Hash::make($req->password)
        ]);
    }
}
