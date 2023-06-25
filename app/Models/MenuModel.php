<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MenuModel extends Model
{
    use HasFactory;
    protected $table = "menu";
    protected $primarykey = "id_menu";
    public $timestamps = false;
    public $fillable = [
        'nama_menu',
        'jenis',
        'deskripsi',
        'gambar',
        'harga',
        'qty'
    ];
}
