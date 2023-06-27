<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Advert extends Model
{
    protected $guarded = [];

    public function users(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(User::class);
    }
    public function description(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(AdvertDescription::class);
    }
    public function photos(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(AdvertPhoto::class);
    }
    public function premium(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(PremiumAdvert::class);
    }
    public function vip(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(VipAdvert::class);
    }
}
