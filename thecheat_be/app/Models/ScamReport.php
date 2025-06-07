<?php

namespace App\Models;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class ScamReport extends Model
{
    protected $table = 'scam_reports';
    use SoftDeletes;
    // protected $fillable = [
    //     'report_type',
    //     'site_type_id',
    //     'item_type_id',
    //     'item_name',
    //     'crypto_type',
    //     'crypto_wallet_address',
    //     'crypto_amount',
    //     'scammer_bank_id',
    //     'scammer_bank_account_name',
    //     'scammer_bank_account_number',
    //     'scammer_bank_amount',
    //     'scammer_bank_sent_date',
    //     'scammer_phone_number',
    //     'scammer_sex',
    //     'scammer_id',
    //     'description',
    //     'victim_name',
    // ];
    protected $guarded = [];

    public function itemType()
    {
        return $this->belongsTo(ItemType::class, 'item_type_id');
    }
}
