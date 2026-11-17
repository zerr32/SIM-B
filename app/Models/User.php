<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use App\Support\Phone;
use Illuminate\Database\Eloquent\SoftDeletes;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes;

    protected $fillable = ['name', 'username', 'email', 'phone', 'password', 'role', 'created_by'];
    protected $hidden = ['password', 'remember_token'];

    public function creator() { return $this->belongsTo(User::class, 'created_by')->withTrashed(); }

    public function setPhoneAttribute($value) { $this->attributes['phone'] = Phone::normalize($value); }

    public function isOwner(): bool { return $this->role === 'owner'; }
    public function isAdmin(): bool { return $this->role === 'admin'; }

    // Boleh mengelola (edit/hapus) akun target?
    public function canManage(User $target): bool
    {
        if ($this->isOwner()) return true;
        return $this->isAdmin() && $target->isAdmin() && $this->id !== $target->id;
    }

    public function canUpdateAccountDetails(User $target): bool
    {
        return $this->canManage($target)
            || ($this->isAdmin() && $this->id === $target->id);
    }

    // Boleh mengganti password akun target?
    public function canChangePasswordOf(User $target): bool
    {
        if (! $this->isOwner() && ! $this->isAdmin()) return false;
        return $this->id === $target->id || $this->canManage($target);
    }

    public function canDelete(User $target): bool
    {
        if ($this->isOwner()) return $this->id !== $target->id;
        return $this->isAdmin()
            && $target->isAdmin()
            && $this->id !== $target->id;
    }
}
