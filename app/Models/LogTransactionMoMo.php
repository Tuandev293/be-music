<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LogTransactionMoMo extends Model
{
    use HasFactory;
    protected $table = 'log_transaction_momo';
    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = [];
    const REFUND_PAID = 2;
    const IS_PAID = 1;
    const NOT_PAID = 0;
    const PAYMENT_SUCCESS_STATUS = 0;
    const PAYMENT_PEDDING_STATUS = 7000;
    const THREE_DAY = 1;
    const ONE_YEAR = 2;
    const ONE_MONTH = 3;
    const SIX_MONTH = 4;
}
