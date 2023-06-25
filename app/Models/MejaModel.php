<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MejaModel extends Model
{
    use HasFactory;
    protected $table = "meja";
    protected $primarykey = "id_meja";
    public $timestamps = false;
    public $fillable =[
        'nomor_meja'
    ];
}
