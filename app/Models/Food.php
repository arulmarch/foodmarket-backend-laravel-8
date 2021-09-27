<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;
use PhpParser\Node\Stmt\Return_;

class Food extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name', 'description','ingredients','price','rate','types','picturePath'
    ];


    public function getCreatedAtAttribute($value)
    {
        return Carbon::parse($value)->timespan();
    }

    public function getUpdatedAtAttribute($value)
    {
        return Carbon::parse($value)->timespan();
    }

    public function toArray()
    {
        $toArray = parent::toArray();
        $toArray['picturePath'] = $this->picturePath;
        return $toArray;
    }

    public function getPicturePathAttribute(){
        return url('').Storage::url($this->attributes['picturePath']);
    }
}
