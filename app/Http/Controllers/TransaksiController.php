<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\TransaksiModel;
use App\Models\DetailTrxModel;
use App\Models\MejaModel;
use Illuminate\Support\Facades\DB;

class TransaksiController extends Controller
{
    //Transaksi baru
    public function createNewTransaction(Request $req){
        $validate = Validator::make($req->all(),[
            'tgl_transaksi'=>'required',
            'id_user'=>'required',
            'id_meja'=>'required',
            'nama_pelanggan'=>'required',
            'status'=>'required'
        ]);
        if($validate->fails()){
            return response()->json($validate->errors()->toJson());
        }
        $create = TransaksiModel::create($req->all(),[
            'tgl_transaksi'=>$req->tgl_transaksi,
            'id_user'=>$req->id_user,
            'id_meja'=>$req->id_meja,
            'nama_pelanggan'=>$req->nama_pelanggan,
            'status'=>$req->status
        ]);
        if($create){
            return response()->json(['status_create'=>true]);
        }else{
            return response()->json(['status_create'=>false]);
        }
    }

    //Detail transaksi
    public function createDetailTransaction(Request $req){
        $validate = Validator::make($req->all(),[
            'id_transaksi'=>'required',
            'id_menu'=>'required',
            'harga'=>'required',
            'qty'=>'required'
        ]);
        if($validate->fails()){
            return response()->json($validate->errors()->toJson());
        }
        $create = DetailTrxModel::create($req->all(),[
            'id_transaksi'=>$req->id_transaksi,
            'id_menu'=>$req->id_menu,
            'harga'=>$req->harga,
            'qty'=>$req->qty
        ]);
        if($create){
            return response()->json(['status'=>true, 'message'=>'Pesanan telah ditambahkan.']);
        }else{
            return response()->json(['status'=>false, 'message'=>'Ups, something wrong.']);
        }
    }

    //Perhitungan tagihan
    public function getSubtotal($id_menu){
        $subtotal = DB::table('detail_transaksi')
              ->leftjoin('menu','detail_transaksi.id_menu','=','menu.id_menu')
              ->select('menu.harga * detail_transaksi.qty')
              ->where('detail_transaksi.id_menu',$id_menu)
              ->get();
        return response()->json($subtotal);
    }
    //List transaksi
    public function listTransaction(){
        $list_trx = TransaksiModel::orderby('tgl_transaksi','desc')
                    ->get();
        return response()->json($list_trx);
    }
    
    //List Detail transaksi
    public function getTransaction($id){
        $transaksi = DB::table('transaksi')
                    ->where('transaksi.id_transaksi',$id)
                    ->leftjoin('users', 'transaksi.id_user', '=', 'users.id')
                    ->leftjoin('meja', 'transaksi.id_meja', '=', 'meja.id_meja')
                    ->leftjoin('detail_transaksi', 'transaksi.id_transaksi','=','detail_transaksi.id_transaksi')
                    ->leftjoin('menu','detail_transaksi.id_menu','=','menu.id_menu')
                    ->select('transaksi.*','users.nama_user','meja.nomor_meja','menu.nama_menu','menu.harga','detail_transaksi.qty','detail_transaksi.harga')
                    ->get();
        return response()->json($transaksi);
    }

    //Update status lunas
    public function updateLunas(Request $req, $id){
        $updateLunas = TransaksiModel::where('id_transaksi',$id)
                        ->update(['status'=>$req->status_lunas]);
        if($updateLunas){
            return response()->json(['status'=>true, 'message'=>'Lunas!']);
        }else{
            return response()->json(['status'=>false, 'message'=>'Gagal update status.']);
        }
    }
    
    //Dropdwon nama pelanggan
    public function trxOption(){
        $opt_trx = DB::table('transaksi')
                ->where('status','belum_bayar')
                ->select('id_transaksi','nama_pelanggan')
                ->get();
        return response()->json($opt_trx);
    }

    //Hapus transaksi
    public function deleteTransaksi($id){
        $delete = TransaksiModel::where('id_transaksi',$id)->delete();
        if($delete){
            return response()->json(['status'=>true, 'message'=>'Sukses hapus transaksi.']);
        }else{
            return response()->json(['status'=>false, 'message'=>'Gagal hapus transaksi.']);
        }
    }

    //Dropdwon petugas kasir
    public function getKasir(){
        $list_kasir = DB::table('users')
                    ->where('role','=','kasir')
                    ->select('id','nama_user')
                    ->get();
        return response()->json($list_kasir);
    }

    //Get harga per menu
    public function getHarga($id){
        $harga = DB::table('menu')
                ->where('id_menu', $id)
                ->select('harga')
                ->first();
        return $harga;
    }

    //Get total tagihan
    public function getSumTotal($id){
        $sum = DB::table('detail_transaksi')
                ->where('detail_transaksi.id_transaksi',$id)
                ->leftjoin('transaksi','detail_transaksi.id_transaksi', '=', 'transaksi.id_transaksi')
                ->selectRaw( 'SUM(detail_transaksi.harga) as total')
                ->first();
        return response()->json($sum);
    }

    //Update tagihan
    public function updateTagihan(Request $req, $id){
        $tagihan = DB::table('transaksi')
                ->where('id_transaksi',$id)
                ->update([
                    'tagihan'=>$req->tagihan,
                    'dibayar'=>$req->dibayar,
                    'kembalian'=>$req->kembalian
                ]);
        if($tagihan){
            return response()->json(['status'=>true]);
        }else{
            return response()->json(['status'=>false, 'message'=>'Gagal bayar.']);
        }
    }

    //Get detail untuk bayar
    public function getDetailTrx($id){
        $detail_trx = DB::table('transaksi')
                   ->where('id_transaksi',$id)
                   ->select('id_transaksi','tgl_transaksi', 'nama_pelanggan', 'tagihan')
                   ->first();
        return response()->json($detail_trx);
    }

    //Get meja yang belum terpakai
    public function getMeja(){
        $meja = DB::table('meja')
                ->whereNull('transaksi.id_meja')
                ->leftjoin('transaksi', 'meja.id_meja','=','transaksi.id_meja')
                ->select('meja.id_meja','meja.nomor_meja')
                ->get();
        return $meja;
    }
    //List transaksi berdasarkan filter tanggal
    public function filterTanggal($tgl_start,$tgl_end){
        $end = date('Y-m-d', strtotime($tgl_end. ' + 1 days'));
        $list = TransaksiModel::whereBetween('tgl_transaksi',[date($tgl_start),date($end)])
            ->orderBy('tgl_transaksi','desc')
            ->get();
        return response()->json($list);
    }

    //Menu paling banyak diipesan
    public function getMenuMakananTerbanyak(){
        $sum = DB::table('detail_transaksi')
            ->where('menu.jenis','makanan')
            ->leftjoin('menu','detail_transaksi.id_menu','=','menu.id_menu')
            ->selectRaw('detail_transaksi.id_menu, menu.nama_menu, sum(detail_transaksi.qty) as jumlah')
            ->groupByRaw('detail_transaksi.id_menu, menu.nama_menu')
            ->orderBy('jumlah','desc')
            ->limit(1)
            ->first();
        
        return response()->json($sum);
    }

    //Minuman paling banyak dipesan
    public function getMenuMinumanTerbanyak(){
        $sum = DB::table('detail_transaksi')
            ->where('menu.jenis','minuman')
            ->leftjoin('menu','detail_transaksi.id_menu','=','menu.id_menu')
            ->selectRaw('detail_transaksi.id_menu, menu.nama_menu, sum(detail_transaksi.qty) as jumlah')
            ->groupByRaw('detail_transaksi.id_menu, menu.nama_menu')
            ->orderBy('jumlah','desc')
            ->limit(1)
            ->first();
        
        return response()->json($sum);
    }
    
    public function getMinFood(){
        $sum = DB::table('detail_transaksi')
            ->where('menu.jenis','makanan')
            ->leftjoin('menu','detail_transaksi.id_menu','=','menu.id_menu')
            ->selectRaw('detail_transaksi.id_menu, menu.nama_menu, sum(detail_transaksi.qty) as jumlah')
            ->groupByRaw('detail_transaksi.id_menu, menu.nama_menu')
            ->orderBy('jumlah','asc')
            ->limit(1)
            ->first();
        
        return response()->json($sum);
    }

    public function getMinDrink(){
        $sum = DB::table('detail_transaksi')
            ->where('menu.jenis','minuman')
            ->leftjoin('menu','detail_transaksi.id_menu','=','menu.id_menu')
            ->selectRaw('detail_transaksi.id_menu, menu.nama_menu, sum(detail_transaksi.qty) as jumlah')
            ->groupByRaw('detail_transaksi.id_menu, menu.nama_menu')
            ->orderBy('jumlah','asc')
            ->limit(1)
            ->first();
        
        return response()->json($sum);
    }

    public function getDetailTrxManager($id){
        $data = DB::table('transaksi')
                ->where('transaksi.id_transaksi',$id)
                ->join('users', 'transaksi.id_user', '=', 'users.id')
                ->join('meja', 'transaksi.id_meja', '=', 'meja.id_meja')
                ->select('transaksi.*','users.nama_user','meja.nomor_meja')
                ->first();
        return response()->json($data);
    }

    public function getDetailPesananManager($id){
        $data = DetailTrxModel::where('id_transaksi',$id)
                ->join('menu','detail_transaksi.id_menu','=','menu.id_menu')
                ->select('detail_transaksi.*','menu.nama_menu')
                ->get();
        return response()->json($data);
    }

    public function getIncome(){
        $get = TransaksiModel::selectRaw('sum(tagihan) as income')->first();
        return response()->json($get);
    }

    public function getTerjual(){
        $get = DetailTrxModel::selectRaw('sum(qty) as total')->first();
        return response()->json($get);
    }
    public function getTotalTrx(){
        $get = TransaksiModel::selectRaw('count(id_transaksi) as jumlah')->first();
        return response()->json($get);
    }


    public function creatInsertID(Request $req){
        $validate = Validator::make($req->all(),[
            'tgl_transaksi'=>'required',
            'id_user'=>'required',
            'id_meja'=>'required',
            'nama_pelanggan'=>'required',
            'status'=>'required'
        ]);
        if($validate->fails()){
            return response()->json($validate->errors()->toJson());
        }
        $create = TransaksiModel::create($req->all(),[
            'tgl_transaksi'=>$req->tgl_transaksi,
            'id_user'=>$req->id_user,
            'id_meja'=>$req->id_meja,
            'nama_pelanggan'=>$req->nama_pelanggan,
            'status'=>$req->status
        ]);
        if($create){
            return response()->json(['id'=>$create->id]);
        }else{
            return response()->json(['status_create'=>false]);
        }
    }
}
