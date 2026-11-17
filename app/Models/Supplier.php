<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Support\Phone;

class Supplier extends Model
{
    protected $fillable = ['name', 'email', 'phone', 'address'];

    public function items() { return $this->hasMany(Item::class); }

    public function setPhoneAttribute($value) { $this->attributes['phone'] = Phone::normalize($value); }

    // Dipakai tombol Chat WA: <a href="<?= e($supplier->wa_link) >">
    public function getWaLinkAttribute(): string { return 'https://wa.me/' . $this->phone; }
}
