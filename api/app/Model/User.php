<?php
/**
 * 订单表
 * @author zhunian
 */
namespace App;

use Illuminate\Database\Eloquent\Model;

class User extends  Model
{
    protected $table = 'user';
    protected $primaryKey = 'user_id';
}

?>