<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\MenuModel;
use File;

class MenuController extends Controller
{
    public function getMenu(){
        $list_menu = MenuModel::get();
        return response()->json($list_menu);
    }

    public function createMenu(Request $req){
        $validate = Validator::make($req->all(),[
            'nama_menu'=>'required',
            'jenis'=>'required',
            'deskripsi'=>'required',
            'gambar'=>'required',
            'harga'=>'required'
        ]);
        if($validate->fails()){
            return response()->json($validate->errors()->toJson());
        }
        $name = $req->file('gambar')->getClientOriginalName(); 
        $path = $req->file('gambar')->store('public/foto_produk');
        $save = new File;
        $save->name = $name;
        $save->path = $path;
        $create = MenuModel::create([
            'nama_menu'=>$req->nama_menu,
            'jenis'=>$req->jenis,
            'deskripsi'=>$req->deskripsi,
            'gambar'=>$name,
            'harga'=>$req->harga
        ]);
        if($create){
            return response()->json(['status'=>true,  'message'=>'Menu baru berhasil ditambahkan.']);
        }else{
            return response()->json(['status'=>false, 'message'=>'Gagal menambahkan menu.']);
        }
    }
    
    public function updateMenu(Request $req, $id){
        $validate = Validator::make($req->all(),[
            'nama_menu'=>'required',
            'jenis'=>'required',
            'deskripsi'=>'required',
            'harga'=>'required'
        ]);
        if($validate->fails()){
            return response()->json($validate->errors()->toJson());
        }
        $update = MenuModel::where('id_menu',$id)->update([
            'nama_menu'=>$req->get('nama_menu'),
            'jenis'=>$req->get('jenis'),
            'deskripsi'=>$req->get('deskripsi'),
            'harga'=>$req->get('harga')
        ]);
        if($update){
            return response()->json(['status'=>true,  'message'=>'Menu berhasil diedit.']);
        }else{
            return response()->json(['status'=>false, 'message'=>'Gagal mengedit menu']);
        }
    }

    public function deleteMenu($id){
        $delete = MenuModel::where('id_menu',$id)->delete();
        if($delete){
            return response()->json(['status'=>true, 'message'=>'Menu berhasil dihapus.']);
        }else{
            return response()->json(['status'=>false, 'message'=>'Gagal menghapus menu.']);
        }
    }

    public function getDetailMenu($id){
        $detail_menu = MenuModel::where('id_menu',$id)->first();
        return response()->json($detail_menu);
    }

    public function getFilterMenu($nama_menu){
        $menu = MenuModel::where('nama_menu', 'like','%'.$nama_menu.'%')
                ->get();
        return response()->json($menu);
    }
}
