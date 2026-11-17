<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AccessLog extends Model
{
    public $timestamps = false;

    protected $fillable = ['user_id', 'target_user_id', 'action_type', 'description'];
    protected $casts = ['created_at' => 'datetime'];

    public function user() { return $this->belongsTo(User::class)->withTrashed(); }
    public function target() { return $this->belongsTo(User::class, 'target_user_id')->withTrashed(); }

    public static function record(string $action, string $description, ?int $targetId = null): void
    {
        static::create([
            'user_id' => auth()->id(),
            'target_user_id' => $targetId,
            'action_type' => $action,
            'description' => $description,
        ]);
    }
}
