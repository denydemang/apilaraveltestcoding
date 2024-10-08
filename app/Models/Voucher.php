<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Voucher extends Model
{
    use HasFactory;
    protected $table ="vouchers";
    protected $primaryKey ="id";
    protected $keyType ="int";
    public $timestamps =true;
    public $incrementing =true;
    protected $guarded = [];
}
