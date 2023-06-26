<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Advert extends Model
{
    protected $guarded =  [];
    public function description(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(AdvertDescription::class);
    }
    public function photos(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(AdvertPhoto::class);
    }

}
