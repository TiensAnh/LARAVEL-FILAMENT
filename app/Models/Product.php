<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class Product extends Model
{
    use HasFactory;

    protected $table = 'sv23810310080_products';

    protected $fillable = [
        'category_id',
        'name',
        'slug',
        'description',
        'price',
        'stock_quantity',
        'image_path',
        'status',
        'warranty_period',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'stock_quantity' => 'integer',
        'warranty_period' => 'integer',
    ];

    protected static function booted(): void
    {
        static::saving(function (Product $product): void {
            if (! $product->slug && $product->name) {
                $product->slug = Str::slug($product->name);
            }

            if ($product->stock_quantity <= 0 && $product->status === 'published') {
                $product->status = 'out_of_stock';
            }
        });
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    public function getWarrantyLabelAttribute(): string
    {
        return $this->warranty_period > 0
            ? sprintf('%s tháng', $this->warranty_period)
            : 'Không bảo hành';
    }
}
