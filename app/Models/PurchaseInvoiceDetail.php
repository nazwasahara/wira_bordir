<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PurchaseInvoiceDetail extends Model
{
    use HasFactory;

    const TYPE_MATERIAL = 'material';
    const TYPE_MATERIAL_COLOR = 'material_color';
    const TYPE_RIBBON_COLOR = 'ribbon_color';
    const TYPE_LACE = 'lace';
    const TYPE_ROMBE = 'rombe';
    const TYPE_MOTIF_RIBBON = 'motif_ribbon';
    const TYPE_ADDITIONAL_ITEM = 'additional_item';
    const TYPE_ADDITIONAL_ITEM_OPTION = 'additional_item_option';

    protected $fillable = [
        'invoice_id',
        'item_type',
        'item_id',
        'quantity',
        'unit_price',
    ];

    protected $casts = [
        'quantity' => 'integer',
        'unit_price' => 'decimal:2',
    ];

    /**
     * Relationships
     */
    public function invoice()
    {
        return $this->belongsTo(PurchaseInvoice::class, 'invoice_id');
    }

    /**
     * ✅ Get the actual item based on type
     */
    public function getItemAttribute()
    {
        return match ($this->item_type) {
            self::TYPE_MATERIAL => Material::find($this->item_id),
            self::TYPE_MATERIAL_COLOR => MaterialColor::find($this->item_id),
            self::TYPE_RIBBON_COLOR => RibbonColor::find($this->item_id),
            self::TYPE_LACE => LaceOption::find($this->item_id),
            self::TYPE_ROMBE => RombeOption::find($this->item_id),
            self::TYPE_MOTIF_RIBBON => MotifRibbonOption::find($this->item_id),
            self::TYPE_ADDITIONAL_ITEM => AdditionalItem::find($this->item_id),
            self::TYPE_ADDITIONAL_ITEM_OPTION => AdditionalItemOption::find($this->item_id),
            default => null,
        };
    }

    /**
     * ✅ Get item name
     */
    public function getItemNameAttribute()
    {
        $item = $this->item;

        if (! $item) {
            return 'Item tidak ditemukan';
        }

        // For items with 'name' attribute
        if (isset($item->name)) {
            return $item->name;
        }

        // For LaceOption, RombeOption, MotifRibbonOption (color + size)
        if (isset($item->color) && isset($item->size)) {
            return "{$item->color} - {$item->size}";
        }

        // For AdditionalItemOption (additional_item name + color + model)
        if ($this->item_type === self::TYPE_ADDITIONAL_ITEM_OPTION && isset($item->color) && isset($item->model)) {
            $additionalItem = $item->additionalItem;
            $itemName = $additionalItem ?  $additionalItem->name : 'Unknown';
            return "{$itemName} - {$item->color} - {$item->model}";
        }

        return 'Unknown Item';
    }

    /**
     * ✅ Get item type text
     */
    public function getItemTypeTextAttribute()
    {
        return match ($this->item_type) {
            self::TYPE_MATERIAL => 'Material',
            self::TYPE_MATERIAL_COLOR => 'Warna Material',
            self::TYPE_RIBBON_COLOR => 'Warna Pita',
            self::TYPE_LACE => 'Renda',
            self::TYPE_ROMBE => 'Rombe',
            self::TYPE_MOTIF_RIBBON => 'Motif Pita',
            self::TYPE_ADDITIONAL_ITEM => 'Item Tambahan',
            self::TYPE_ADDITIONAL_ITEM_OPTION => 'Opsi Item Tambahan',
            default => ucfirst(str_replace('_', ' ', $this->item_type)),
        };
    }

    /**
     * ✅ Calculate subtotal (quantity * unit_price)
     */
    public function getSubtotalAttribute()
    {
        return $this->quantity * $this->unit_price;
    }

    /**
     * Get formatted unit price
     */
    public function getFormattedUnitPriceAttribute()
    {
        return 'Rp ' .  number_format($this->unit_price, 0, ',', '.');
    }

    /**
     * Get formatted subtotal
     */
    public function getFormattedSubtotalAttribute()
    {
        return 'Rp ' . number_format($this->subtotal, 0, ',', '.');
    }

    /**
     * Get all item types
     */
    public static function getItemTypes(): array
    {
        return [
            self::TYPE_MATERIAL => 'Material',
            self::TYPE_MATERIAL_COLOR => 'Warna Material',
            self::TYPE_RIBBON_COLOR => 'Warna Pita',
            self::TYPE_LACE => 'Renda',
            self::TYPE_ROMBE => 'Rombe',
            self::TYPE_MOTIF_RIBBON => 'Motif Pita',
            self::TYPE_ADDITIONAL_ITEM => 'Item Tambahan',
            self::TYPE_ADDITIONAL_ITEM_OPTION => 'Opsi Item Tambahan',
        ];
    }
}
