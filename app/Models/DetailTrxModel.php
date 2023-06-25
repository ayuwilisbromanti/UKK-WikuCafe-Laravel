<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DetailTrxModel extends Model
{
    use HasFactory;
    protected $table = "detail_transaksi";
    protected $primarykey = "id_transaksi";
    public $timestamps = false;
    public $fillable = [
        'id_transaksi',
        'id_menu',
        'harga',
        'qty'
    ];
}
