<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LogCurrencyOperation extends Model
{
    public $timestamps = false;
    protected $table = 'log_currency_operation';
    protected $primaryKey = 'id';

    protected $fillable = [
        //只读
    ];

    protected $hidden = [
        //
    ];
}
