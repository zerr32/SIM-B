<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Mutation extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'item_id', 'user_id', 'item_code', 'item_name', 'type', 'unit', 'recipient_name',
        'quantity', 'previous_stock', 'current_stock', 'unit_price',
        'total_price', 'remaining_value', 'notes',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'unit_price' => 'decimal:2',
        'total_price' => 'decimal:2',
        'remaining_value' => 'decimal:2',
    ];

    public function item() { return $this->belongsTo(Item::class)->withTrashed(); }
    public function user() { return $this->belongsTo(User::class)->withTrashed(); }
}
