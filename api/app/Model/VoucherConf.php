<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class VoucherConf extends Model
{
    protected $table = 'voucher_conf';
    protected $primaryKey = 'vcId';
    public $timestamps = false;
}
