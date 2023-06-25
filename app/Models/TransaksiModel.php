<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TransaksiModel extends Model
{
    use HasFactory;
    protected $table = "transaksi";
    protected $primarykey = "id_transaksi";
    public $timestamps = false;
    public $fillable = [
        'tgl_transaksi',
        'id_user',
        'id_meja',
        'nama_pelanggan',
        'status',
        'tagihan',
        'dibayar',
        'kembalian'
    ];
}
