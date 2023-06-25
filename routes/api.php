<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\UserController;
use App\Http\Controllers\MenuController;
use App\Http\Controllers\MejaController;
use App\Http\Controllers\TransaksiController;
use App\Http\Controllers\AuthController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
Route::get('/list_user',[UserController::class, 'getUser']);

Route::prefix('admin')->controller(AuthController::class)->group(function () {
    Route::post('login', 'login');
    Route::post('register', 'register');
    Route::middleware('auth:admin_api')->group(function () {
        Route::post('logout', 'logout');
        Route::post('me', 'me');
        Route::group(['middleware'=>['api.admin']],function(){
            Route::post('register', 'register');
            Route::get('/list_user',[AuthController::class, 'getKaryawan']);
            Route::get('/detail_act/{id}',[AuthController::class, 'getKaryawanActivity']);
            Route::get('/detail_edit/{id}',[AuthController::class, 'detailKaryawan']);
            Route::put('/update_user/{id}',[AuthController::class, 'updateUser']);
            Route::delete('/delete_user/{id}',[AuthController::class, 'deleteUser']);
            //Menu
            Route::get('/list_menu',[MenuController::class, 'getMenu']);
            Route::post('/create_menu',[MenuController::class, 'createMenu']);
            Route::put('/update_menu/{id}',[MenuController::class, 'updateMenu']);
            Route::delete('/delete_menu/{id}',[MenuController::class, 'deleteMenu']);
            Route::get('/get_detailMenu/{id}',[MenuController::class, 'getDetailMenu']);  

            //Meja
            Route::get('/list_meja',[MejaController::class, 'getMeja']);
            Route::post('/create_meja',[MejaController::class, 'createMeja']);
            Route::put('/update_meja/{id}',[MejaController::class, 'updateMeja']);
            Route::delete('/delete_meja/{id}',[MejaController::class, 'deleteMeja']);
            Route::get('/get_detailMeja/{id}',[MejaController::class, 'getDetailMeja']);
        });
        Route::group(['middleware'=>['api.kasir']],function(){
            //Meja
            Route::get('/list_meja_kasir',[MejaController::class, 'getMeja']);

            //Menu
            Route::get('/list_menu_kasir',[MenuController::class, 'getMenu']);

            //Transaksi
            Route::post('/create_transaksi',[TransaksiController::class, 'createNewTransaction']);
            Route::post('/create_detail',[TransaksiController::class, 'createDetailTransaction']);
            
            Route::get('/list_transaksi/{id}',[TransaksiController::class, 'getTransaction']); // get detail transaksi buat print struk
            Route::get('/trx_list',[TransaksiController::class, 'listTransaction']);
            Route::put('/update_lunas/{id}',[TransaksiController::class, 'updateLunas']);
            Route::get('/transaksi_option',[TransaksiController::class, 'trxOption']);
            Route::get('/get_kasir',[TransaksiController::class, 'getKasir']);
            Route::get('/subtotal/{id}',[TransaksiController::class, 'getSubtotal']);
            Route::delete('/delete_trx/{id}',[TransaksiController::class, 'deleteTransaksi']);
            Route::get('/get_harga/{id}',[TransaksiController::class, 'getHarga']);
            Route::get('/get_sum/{id}',[TransaksiController::class, 'getSumTotal']);
            Route::put('/update_tagihan/{id}',[TransaksiController::class, 'updateTagihan']);
            Route::get('/detail_trx/{id}',[TransaksiController::class, 'getDetailTrx']);
            Route::get('/opt_meja',[TransaksiController::class, 'getMeja']);
            Route::get('/filter_tgl/{tgl_start}/{tgl_end}',[TransaksiController::class, 'filterTanggal']);
            // Route::get('/max_menu',[TransaksiController::class,'getMenuTerbanyak']);

            
        });
        Route::group(['middleware'=>['api.manager']],function(){
            Route::get('/list_transaksi_manager/{id}',[TransaksiController::class, 'getTransaction']); //detail transaksi buat page detail transaksi
            Route::get('/trx_list_manager',[TransaksiController::class, 'listTransaction']);
            Route::get('/filter_tgl_manager/{tgl_start}/{tgl_end}',[TransaksiController::class, 'filterTanggal']);
            Route::get('/detail_trx_parent/{id}',[TransaksiController::class, 'getDetailTrxManager']);
            Route::get('/detail_trx_pesanan/{id}',[TransaksiController::class, 'getDetailPesananManager']);
                  
        });
    });
});
//All Role
Route::get('/max_makan',[TransaksiController::class,'getMenuMakananTerbanyak']);
Route::get('/max_minum',[TransaksiController::class,'getMenuMinumanTerbanyak']);
Route::get('/min_makanan',[TransaksiController::class,'getMinFood']);
Route::get('/min_minuman',[TransaksiController::class,'getMinDrink']);
Route::get('/income',[TransaksiController::class,'getIncome']);
Route::get('/terjual',[TransaksiController::class,'getTerjual']);
Route::get('/total_trx',[TransaksiController::class,'getTotalTrx']);

//User
// Route::get('/list_user',[AuthController::class, 'getKaryawan']);
// Route::get('/detail_act/{id}',[AuthController::class, 'getKaryawanActivity']);
// Route::get('/detail_edit/{id}',[AuthController::class, 'detailKaryawan']);
// Route::put('/update_user/{id}',[AuthController::class, 'updateUser']);
// Route::delete('/delete_user/{id}',[AuthController::class, 'deleteUser']);

// //Menu
// Route::get('/list_menu',[MenuController::class, 'getMenu']);
// Route::post('/create_menu',[MenuController::class, 'createMenu']);
// Route::put('/update_menu/{id}',[MenuController::class, 'updateMenu']);
// Route::delete('/delete_menu/{id}',[MenuController::class, 'deleteMenu']);
// Route::get('/get_detailMenu/{id}',[MenuController::class, 'getDetailMenu']);


// //Meja
// Route::get('/list_meja',[MejaController::class, 'getMeja']);
// Route::post('/create_meja',[MejaController::class, 'createMeja']);
// Route::put('/update_meja/{id}',[MejaController::class, 'updateMeja']);
// Route::delete('/delete_meja/{id}',[MejaController::class, 'deleteMeja']);
// Route::get('/get_detailMeja/{id}',[MejaController::class, 'getDetailMeja']);

// //Transaksi
// Route::post('/create_transaksi',[TransaksiController::class, 'createNewTransaction']);
// Route::post('/create_detail',[TransaksiController::class, 'createDetailTransaction']);

// Route::get('/list_transaksi/{id}',[TransaksiController::class, 'getTransaction']);
// Route::get('/trx_list',[TransaksiController::class, 'listTransaction']);
// Route::put('/update_lunas/{id}',[TransaksiController::class, 'updateLunas']);
// // Route::get('/coba_list_transaksi',[TransaksiController::class, 'listTransaction']);
// Route::get('/transaksi_option',[TransaksiController::class, 'trxOption']);
// Route::get('/get_kasir',[TransaksiController::class, 'getKasir']);
// Route::get('/subtotal/{id}',[TransaksiController::class, 'getSubtotal']);
// Route::delete('/delete_trx/{id}',[TransaksiController::class, 'deleteTransaksi']);
// Route::get('/get_harga/{id}',[TransaksiController::class, 'getHarga']);
// Route::get('/get_sum/{id}',[TransaksiController::class, 'getSumTotal']);
// Route::put('/update_tagihan/{id}',[TransaksiController::class, 'updateTagihan']);
// Route::get('/detail_trx/{id}',[TransaksiController::class, 'getDetailTrx']);
// Route::get('/opt_meja',[TransaksiController::class, 'getMeja']);
// Route::get('/filter_tgl/{tgl_start}/{tgl_end}',[TransaksiController::class, 'filterTanggal']);

Route::post('/insert_id',[TransaksiController::class, 'creatInsertID']);