<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\MejaModel;


class MejaController extends Controller
{
    public function getMeja(){
        $list_meja = MejaModel::orderby('id_meja','asc')
        ->get();
        return response()->json($list_meja);
    }

    public function createMeja(Request $req){
        $validate = Validator::make($req->all(),[
            'nomor_meja'=>'required'
        ]);
        if($validate->fails()){
            return response()->json($validate->errors()->toJson());
        }
        $create = MejaModel::create([
            'nomor_meja'=>$req->nomor_meja
        ]);
        if($create){
            return response()->json(['status'=>true, 'message'=>'Sukses menambahkan nomor meja.']);
        }else{
            return response()->json(['status'=>false, 'message'=>'Gagal menambahkan nomor meja']);
        }
    }

    public function updateMeja(Request $req, $id){
        $validate = Validator::make($req->all(),[
            'nomor_meja'=>'required'
        ]);
        if($validate->fails()){
            return response()->json($validate->errors()->toJson());
        }
        $update = MejaModel::where('id_meja',$id)->update([
            'nomor_meja'=>$req->get('nomor_meja')
        ]);
        if($update){
            return response()->json(['status'=>true,  'message'=>'Sukses memperbarui nomor meja.']);
        }else{
            return response()->json(['status'=>false, 'message'=>'Gagal memperbarui nomor meja.']);
        }
    }

    public function deleteMeja($id){
        $delete = MejaModel::where('id_meja',$id)->delete();
        if($delete){
            return response()->json(['status'=>true, 'message'=>'Sukses hapus nomor meja.']);
        }else{
            return response()->json(['status'=>false, 'message'=>'Gagal hapus nomor meja.']);
        }
    }

    public function getDetailMeja($id){
        $get_detail = MejaModel::where('id_meja',$id)->first();
        return response()->json($get_detail);
    }
}
