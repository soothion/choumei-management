<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class OrderTicket extends Model
{
    protected $table = 'order_ticket';
    protected $primaryKey = 'order_ticket_id';
    public $timestamps = false;
}
