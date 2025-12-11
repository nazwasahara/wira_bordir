<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'product_id',
        'material_id',
        'material_color_id',
        'sash_type_id',
        'font_id',
        'side_motif_id',
        'ribbon_color_id',
        'lace_option_id',
        'rombe_option_id',
        'motif_ribbon_option_id',
        'additional_item_option_id',
        'quantity',
        'final_price',
        'text_right',
        'text_left',
        'text_single',
        'logo_path',
    ];

    protected $casts = [
        'final_price' => 'decimal:2',
    ];

    /**
     * Relasi dengan Order
     */
    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * Relasi dengan Product
     */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Relasi dengan Material
     */
    public function material()
    {
        return $this->belongsTo(Material::class);
    }

    /**
     * Relasi dengan MaterialColor
     */
    public function materialColor()
    {
        return $this->belongsTo(MaterialColor::class);
    }

    /**
     * Relasi dengan SashType
     */
    public function sashType()
    {
        return $this->belongsTo(SashType::class);
    }

    /**
     * Relasi dengan Font
     */
    public function font()
    {
        return $this->belongsTo(Font::class);
    }

    /**
     * Relasi dengan SideMotif
     */
    public function sideMotif()
    {
        return $this->belongsTo(SideMotif::class);
    }

    /**
     * Relasi dengan RibbonColor
     */
    public function ribbonColor()
    {
        return $this->belongsTo(RibbonColor::class);
    }

    /**
     * Relasi dengan LaceOption
     */
    public function laceOption()
    {
        return $this->belongsTo(LaceOption::class);
    }

    /**
     * Relasi dengan RombeOption
     */
    public function rombeOption()
    {
        return $this->belongsTo(RombeOption::class);
    }

    /**
     * Relasi dengan MotifRibbonOption
     */
    public function motifRibbonOption()
    {
        return $this->belongsTo(MotifRibbonOption::class);
    }

    /**
     * Relasi dengan AdditionalItemOption
     */
    public function additionalItemOption()
    {
        return $this->belongsTo(AdditionalItemOption::class);
    }

    /**
     * Accessor untuk format harga
     */
    public function getFormattedFinalPriceAttribute()
    {
        return 'Rp ' . number_format($this->final_price, 0, ',', '.');
    }

    /**
     * Accessor untuk subtotal
     */
    public function getSubtotalAttribute()
    {
        return $this->final_price * $this->quantity;
    }

    /**
     * Accessor untuk format subtotal
     */
    public function getFormattedSubtotalAttribute()
    {
        return 'Rp ' . number_format($this->subtotal, 0, ',', '.');
    }

    /**
     * Get price breakdown for this order item
     */
    public function getPriceBreakdownAttribute()
    {
        $breakdown = [];

        // Product base price
        if ($this->product) {
            $breakdown[] = [
                'label' => 'Produk: ' . $this->product->product_name,
                'price' => $this->product->base_price,
            ];
        }

        // Material price
        if ($this->material) {
            $breakdown[] = [
                'label' => 'Material: ' . $this->material->name,
                'price' => $this->material->price,
            ];
        }

        // Material Color price
        if ($this->materialColor) {
            $breakdown[] = [
                'label' => 'Warna Material: ' . $this->materialColor->name,
                'price' => $this->materialColor->price,
            ];
        }

        // Sash Type price
        if ($this->sashType) {
            $breakdown[] = [
                'label' => 'Tipe Sash: ' . $this->sashType->name,
                'price' => $this->sashType->price,
            ];
        }

        // Font price
        if ($this->font) {
            $breakdown[] = [
                'label' => 'Font: ' . $this->font->name,
                'price' => $this->font->price,
            ];
        }

        // Side Motif price
        if ($this->sideMotif) {
            $breakdown[] = [
                'label' => 'Motif Samping: ' . $this->sideMotif->name,
                'price' => $this->sideMotif->price,
            ];
        }

        // Ribbon Color price
        if ($this->ribbonColor) {
            $breakdown[] = [
                'label' => 'Warna Pita: ' . $this->ribbonColor->name,
                'price' => $this->ribbonColor->price,
            ];
        }

        // Lace Option price
        if ($this->laceOption) {
            $breakdown[] = [
                'label' => 'Renda: ' . $this->laceOption->color . ' (' . $this->laceOption->size_indonesia . ')',
                'price' => $this->laceOption->price,
            ];
        }

        // Rombe Option price
        if ($this->rombeOption) {
            $breakdown[] = [
                'label' => 'Rombe: ' . $this->rombeOption->color . ' (' . $this->rombeOption->size_indonesia . ')',
                'price' => $this->rombeOption->price,
            ];
        }

        // Motif Ribbon Option price
        if ($this->motifRibbonOption) {
            $breakdown[] = [
                'label' => 'Pita Motif: ' . $this->motifRibbonOption->color . ' (' . $this->motifRibbonOption->size_indonesia . ')',
                'price' => $this->motifRibbonOption->price,
            ];
        }

        // Additional Item Option price
        if ($this->additionalItemOption) {
            $itemName = $this->additionalItemOption->additionalItem->name ?? 'Item Tambahan';
            $breakdown[] = [
                'label' => $itemName . ': ' . $this->additionalItemOption->color . ' (' . $this->additionalItemOption->model . ')',
                'price' => $this->additionalItemOption->price,
            ];
        }

        return $breakdown;
    }

    /**
     * Get total from breakdown
     */
    public function getCalculatedPriceAttribute()
    {
        return collect($this->price_breakdown)->sum('price');
    }

    /**
     * Format calculated price
     */
    public function getFormattedCalculatedPriceAttribute()
    {
        return 'Rp ' . number_format($this->calculated_price, 0, ',', '.');
    }
}
