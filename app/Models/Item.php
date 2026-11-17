<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Item extends Model
{
    use SoftDeletes;

    protected $fillable = ['code', 'category_id', 'supplier_id', 'name', 'unit', 'stock', 'purchase_price'];
    protected $casts = ['purchase_price' => 'decimal:2'];

    public function category() { return $this->belongsTo(Category::class); }
    public function supplier() { return $this->belongsTo(Supplier::class); }
    public function mutations() { return $this->hasMany(Mutation::class); }

    public function getTotalAssetAttribute() { return $this->stock * $this->purchase_price; }

    // Kode otomatis: BRG-YYYYMMDD-001
    public static function generateCode(): string
    {
        $prefix = 'BRG-' . now()->format('Ymd') . '-';
        $last = static::withTrashed()->where('code', 'like', $prefix . '%')->orderByDesc('code')->value('code');
        $next = $last ? ((int) substr($last, strlen($prefix))) + 1 : 1;
        return $prefix . str_pad($next, 3, '0', STR_PAD_LEFT);
    }
}
