<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'food_id','user_id','quantity','total','status','payment_url'
    ];


    public function food(){
        $this->hasOne(Food::class,'id','food_id');
    }

    public function user(){
        $this->hasOne(User::class,'id','user_id');
    }


    public function getCreatedAtAttribute($value)
    {
        return Carbon::parse($value)->timespan();
    }

    public function getUpdatedAtAttribute($value)
    {
        return Carbon::parse($value)->timespan();
    }



}
