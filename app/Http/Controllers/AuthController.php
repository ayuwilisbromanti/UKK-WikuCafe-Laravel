<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use auth;
use Validator;
use Illuminate\Support\Facades\DB;
// use Tymon\JWTAuth\Facades\JWTAuth as FacadesJWTAuth;
// use Illuminate\Support\Facades\Auth;



class AuthController extends Controller
{
    public function login(Request $req)
    {
        $credentials = $req->only('username', 'password');
        if (!$token = auth()->guard('admin_api')->attempt($credentials)) {
            return response()->json(['error' => 'Tidak terdaftar. Tolong periksa kembali username dan password anda.'], 401);
        }
        $user = User::where('username', $req->username)->first();
        return $this->respondWithToken($token, $user);
        // return response()->json(compact());
    }

    public function me()
    {
        return response()->json(auth('admin_api')->user());
    }

    public function logout()
    {
        auth('admin_api')->logout();

        return response()->json(['message' => 'Successfully logged out']);
    }

    public function refresh()
    {
        return $this->respondWithToken(auth('admin_api')->refresh());
    }

    protected function respondWithToken($token,$user)
    {
        
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth()->guard('admin_api')->factory()->getTTL() * 60,
            'data_user'=>$user
        ]);
    }

    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nama_user' => 'required|string|between:2,100',
            'role' => 'required',
            'username' => 'required|string|max:100|unique:users',
            'password' => 'required|string|min:6',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors()->toJson(), 400);
        }

        $user = User::create(array_merge(
            $validator->validated(),
            ['password' => bcrypt($request->password)]
        ));

        return response()->json([
            'message' => 'Berhasil menambahkan data staff.',
            'status'=>true
            // 'user' => $user
        ], 201);
    }

    public function getKaryawan(){
        $list = DB::table('users')
            ->leftjoin('transaksi','transaksi.id_user','=','users.id')
            ->selectRaw('users.id, users.nama_user, users.role, users.username, count(transaksi.id_user) as jumlah_trx')
            ->groupBy('users.id', 'users.nama_user', 'users.role', 'users.username')
            ->orderBy('users.id','desc')
            ->get();
        return response()->json($list);
    }

    public function getKaryawanActivity($id){
        $get_karyawan_act = DB::table('transaksi')
                        ->where('id_user', $id)
                        ->join('detail_transaksi', 'transaksi.id_transaksi','=','detail_transaksi.id_transaksi')
                        ->selectRaw('transaksi.tgl_transaksi, detail_transaksi.id_transaksi, transaksi.nama_pelanggan, sum(detail_transaksi.harga) as subtotal, count(transaksi.id_user) as jumlah_trx')
                        ->groupBy('transaksi.tgl_transaksi', 'detail_transaksi.id_transaksi', 'transaksi.nama_pelanggan')
                        ->get();
        return response()->json($get_karyawan_act);
    }

    public function detailKaryawan($id){
        $get_detail = User::where('id',$id)
                    ->first();
        return response()->json($get_detail);
    }

    public function updateUser(Request $req, $id){
        $validate = Validator::make($req->all(),[
            'nama_user'=>'required|string|max:255',
            'role'=>'required',
            'username'=>'required'
        ]);
        if($validate->fails()){
            return response()->json($validate->errors()->toJson(), 400);
        }
        $update = User::where('id',$id)
                    ->update([
                        'nama_user'=>$req->nama_user,
                        'role'=>$req->role,
                        'username'=>$req->username
                    ]);
        if($update){
            return response()->json(['status'=>true,  'message'=>'Sukses update User.']);
        }else{
            return response()->json(['status'=>false, 'message'=>'Gagal update user.']);
        }
    }

    public function deleteUser($id){
        $delete = User::where('id',$id)->delete();
        if($delete){
            return response()->json(['status'=>true]);
        }else{
            return response()->josn(['status'=>false]);
        }
    }
}