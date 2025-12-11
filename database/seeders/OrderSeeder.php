<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\User;
use App\Models\Product;
use App\Models\Material;
use App\Models\MaterialColor;
use App\Models\SashType;
use App\Models\Font;
use App\Models\SideMotif;
use App\Models\RibbonColor;
use App\Models\LaceOption;
use App\Models\RombeOption;
use App\Models\MotifRibbonOption;
use App\Models\AdditionalItemOption;
use App\Models\CancelledTransaction;

class OrderSeeder extends Seeder
{
  /**
   * Run the database seeds.
   */
  public function run(): void
  {
    $this->command->info('🌱 Starting Order Seeder...');

    // Check if required data exists
    if (!User::exists()) {
      $this->command->error('❌ No users found! Please seed users first.');
      return;
    }

    if (!Product::exists()) {
      $this->command->error('❌ No products found! Please seed products first.');
      return;
    }

    // Ambil data pertama dari setiap table
    $user = User::where('role', 'customer')->first() ?? User::first();
    $product = Product::where('is_active', true)->first() ?? Product::first();
    $material = Material::first();
    $materialColor = MaterialColor::first();
    $sashType = SashType::first();
    $font = Font::first();
    $sideMotif = SideMotif::first();
    $ribbonColor = RibbonColor::first();
    $laceOption = LaceOption::first();
    $rombeOption = RombeOption::first();
    $motifRibbonOption = MotifRibbonOption::first();
    $additionalItemOption = AdditionalItemOption::first();

    // Hitung total price untuk 1 item
    $itemPrice = 0;
    $itemPrice += $product ? $product->base_price : 50000;
    $itemPrice += $material ? $material->price : 0;
    $itemPrice += $materialColor ? $materialColor->price : 0;
    $itemPrice += $sashType ? $sashType->price : 0;
    $itemPrice += $font ? $font->price : 0;
    $itemPrice += $sideMotif ? $sideMotif->price : 0;
    $itemPrice += $ribbonColor ? $ribbonColor->price : 0;
    $itemPrice += $laceOption ? $laceOption->price : 0;
    $itemPrice += $rombeOption ? $rombeOption->price : 0;
    $itemPrice += $motifRibbonOption ? $motifRibbonOption->price : 0;
    $itemPrice += $additionalItemOption ? $additionalItemOption->price : 0;

    $quantity = 2; // Buat 2 pcs
    $totalPrice = $itemPrice * $quantity;

    $this->command->info("💰 Calculated item price: Rp " . number_format($itemPrice, 0, ',', '.'));
    $this->command->info("💰 Total price for {$quantity} items: Rp " . number_format($totalPrice, 0, ',', '.'));

    // ========================================
    // ORDER 1: Pending (Belum bayar)
    // ========================================
    $this->command->info('Creating Order 1: Pending...');

    $order1 = Order::create([
      'user_id' => $user->id,
      'customer_name' => $user->username ?? 'John Doe',
      'customer_phone_number' => '081234567890',
      'customer_address' => 'Jl. Contoh No. 123, Jakarta Selatan',
      'total_price' => $totalPrice,
      'amount_paid' => 0,
      'payment_proof' => null,
      'status' => 'pending',
      'created_at' => now()->subDays(5),
      'updated_at' => now()->subDays(5),
    ]);

    OrderItem::create([
      'order_id' => $order1->id,
      'product_id' => $product->id ?? null,
      'material_id' => $material->id ?? null,
      'material_color_id' => $materialColor->id ?? null,
      'sash_type_id' => $sashType->id ?? null,
      'font_id' => $font->id ?? null,
      'side_motif_id' => $sideMotif->id ?? null,
      'ribbon_color_id' => $ribbonColor->id ?? null,
      'lace_option_id' => $laceOption->id ?? null,
      'rombe_option_id' => $rombeOption->id ?? null,
      'motif_ribbon_option_id' => $motifRibbonOption->id ?? null,
      'additional_item_option_id' => $additionalItemOption->id ?? null,
      'quantity' => $quantity,
      'final_price' => $itemPrice,
    ]);

    $this->command->info('✅ Order 1 (Pending) created - ' . $order1->order_number);

    // ========================================
    // ORDER 2: Paid (Sudah bayar, belum dikonfirmasi)
    // ========================================
    $this->command->info('Creating Order 2: Paid...');

    $order2 = Order::create([
      'user_id' => $user->id,
      'customer_name' => $user->username ?? 'Jane Smith',
      'customer_phone_number' => '081234567891',
      'customer_address' => 'Jl. Melati No. 456, Bandung',
      'total_price' => $totalPrice,
      'amount_paid' => $totalPrice,
      'payment_proof' => null,
      'status' => 'paid',
      'created_at' => now()->subDays(4),
      'updated_at' => now()->subDays(4),
    ]);

    OrderItem::create([
      'order_id' => $order2->id,
      'product_id' => $product->id ?? null,
      'material_id' => $material->id ?? null,
      'material_color_id' => $materialColor->id ?? null,
      'sash_type_id' => $sashType->id ?? null,
      'font_id' => $font->id ?? null,
      'side_motif_id' => $sideMotif->id ?? null,
      'ribbon_color_id' => $ribbonColor->id ?? null,
      'lace_option_id' => $laceOption->id ?? null,
      'rombe_option_id' => $rombeOption->id ?? null,
      'motif_ribbon_option_id' => $motifRibbonOption->id ?? null,
      'additional_item_option_id' => $additionalItemOption->id ?? null,
      'quantity' => $quantity,
      'final_price' => $itemPrice,
    ]);

    $this->command->info('✅ Order 2 (Paid) created - ' . $order2->order_number);

    // ========================================
    // ORDER 3: Confirm (Pembayaran dikonfirmasi)
    // ========================================
    $this->command->info('Creating Order 3: Confirm...');

    $order3 = Order::create([
      'user_id' => $user->id,
      'customer_name' => $user->username ?? 'Bob Wilson',
      'customer_phone_number' => '081234567892',
      'customer_address' => 'Jl. Anggrek No. 789, Surabaya',
      'total_price' => $totalPrice,
      'amount_paid' => $totalPrice,
      'payment_proof' => null,
      'status' => 'confirm',
      'created_at' => now()->subDays(3),
      'updated_at' => now()->subDays(3),
    ]);

    OrderItem::create([
      'order_id' => $order3->id,
      'product_id' => $product->id ?? null,
      'material_id' => $material->id ?? null,
      'material_color_id' => $materialColor->id ?? null,
      'sash_type_id' => $sashType->id ?? null,
      'font_id' => $font->id ?? null,
      'side_motif_id' => $sideMotif->id ?? null,
      'ribbon_color_id' => $ribbonColor->id ?? null,
      'lace_option_id' => $laceOption->id ?? null,
      'rombe_option_id' => $rombeOption->id ?? null,
      'motif_ribbon_option_id' => $motifRibbonOption->id ?? null,
      'additional_item_option_id' => $additionalItemOption->id ?? null,
      'quantity' => $quantity,
      'final_price' => $itemPrice,
    ]);

    $this->command->info('✅ Order 3 (Confirm) created - ' . $order3->order_number);

    // ========================================
    // ORDER 4: Processing (Sedang dikerjakan)
    // ========================================
    $this->command->info('Creating Order 4: Processing...');

    $order4 = Order::create([
      'user_id' => $user->id,
      'customer_name' => $user->username ?? 'Alice Brown',
      'customer_phone_number' => '081234567893',
      'customer_address' => 'Jl. Mawar No. 321, Yogyakarta',
      'total_price' => $totalPrice * 1.5,
      'amount_paid' => $totalPrice * 1.5,
      'payment_proof' => null,
      'status' => 'processing',
      'created_at' => now()->subDays(2),
      'updated_at' => now()->subDays(1),
    ]);

    OrderItem::create([
      'order_id' => $order4->id,
      'product_id' => $product->id ?? null,
      'material_id' => $material->id ?? null,
      'material_color_id' => $materialColor->id ?? null,
      'sash_type_id' => $sashType->id ?? null,
      'font_id' => $font->id ?? null,
      'side_motif_id' => $sideMotif->id ?? null,
      'ribbon_color_id' => $ribbonColor->id ?? null,
      'lace_option_id' => $laceOption->id ?? null,
      'rombe_option_id' => $rombeOption->id ?? null,
      'motif_ribbon_option_id' => $motifRibbonOption->id ?? null,
      'additional_item_option_id' => $additionalItemOption->id ?? null,
      'quantity' => 3,
      'final_price' => $itemPrice,
    ]);

    $this->command->info('✅ Order 4 (Processing) created - ' . $order4->order_number);

    // ========================================
    // ORDER 5: Done (Selesai)
    // ========================================
    $this->command->info('Creating Order 5: Done...');

    $order5 = Order::create([
      'user_id' => $user->id,
      'customer_name' => $user->username ?? 'Charlie Davis',
      'customer_phone_number' => '081234567894',
      'customer_address' => 'Jl. Kenanga No. 654, Semarang',
      'total_price' => $totalPrice,
      'amount_paid' => $totalPrice,
      'payment_proof' => null,
      'status' => 'done',
      'created_at' => now()->subDays(10),
      'updated_at' => now()->subDays(5),
    ]);

    OrderItem::create([
      'order_id' => $order5->id,
      'product_id' => $product->id ?? null,
      'material_id' => $material->id ?? null,
      'material_color_id' => $materialColor->id ?? null,
      'sash_type_id' => $sashType->id ?? null,
      'font_id' => $font->id ?? null,
      'side_motif_id' => $sideMotif->id ?? null,
      'ribbon_color_id' => $ribbonColor->id ?? null,
      'lace_option_id' => $laceOption->id ?? null,
      'rombe_option_id' => $rombeOption->id ?? null,
      'motif_ribbon_option_id' => $motifRibbonOption->id ?? null,
      'additional_item_option_id' => $additionalItemOption->id ?? null,
      'quantity' => $quantity,
      'final_price' => $itemPrice,
    ]);

    $this->command->info('✅ Order 5 (Done) created - ' . $order5->order_number);

    // ========================================
    // ORDER 6: Cancel (Dibatalkan)
    // ========================================
    $this->command->info('Creating Order 6: Cancel...');

    $order6 = Order::create([
      'user_id' => $user->id,
      'customer_name' => $user->username ?? 'David Lee',
      'customer_phone_number' => '081234567895',
      'customer_address' => 'Jl. Tulip No. 987, Malang',
      'total_price' => $totalPrice,
      'amount_paid' => 0,
      'payment_proof' => null,
      'status' => 'cancel',
      'created_at' => now()->subDays(7),
      'updated_at' => now()->subDays(6),
    ]);

    OrderItem::create([
      'order_id' => $order6->id,
      'product_id' => $product->id ?? null,
      'material_id' => $material->id ?? null,
      'material_color_id' => $materialColor->id ?? null,
      'sash_type_id' => $sashType->id ?? null,
      'font_id' => $font->id ?? null,
      'side_motif_id' => $sideMotif->id ?? null,
      'ribbon_color_id' => $ribbonColor->id ?? null,
      'lace_option_id' => $laceOption->id ?? null,
      'rombe_option_id' => $rombeOption->id ?? null,
      'motif_ribbon_option_id' => $motifRibbonOption->id ?? null,
      'additional_item_option_id' => $additionalItemOption->id ?? null,
      'quantity' => $quantity,
      'final_price' => $itemPrice,
    ]);

    // Tambahkan cancelled transaction
    CancelledTransaction::create([
      'order_id' => $order6->id,
      'cancellation_date' => now()->subDays(6),
      'cancellation_reason' => 'Pelanggan berubah pikiran dan meminta pembatalan pesanan.',
    ]);

    $this->command->info('✅ Order 6 (Cancel) created - ' . $order6->order_number);

    $this->command->info('');
    $this->command->info('🎉 Order Seeder completed successfully!');
    $this->command->info('📊 Summary:');
    $this->command->info('   ✅ 1 Pending Order (belum bayar)');
    $this->command->info('   ✅ 1 Paid Order (sudah bayar, belum dikonfirmasi)');
    $this->command->info('   ✅ 1 Confirm Order (pembayaran dikonfirmasi)');
    $this->command->info('   ✅ 1 Processing Order (sedang dikerjakan)');
    $this->command->info('   ✅ 1 Done Order (selesai)');
    $this->command->info('   ✅ 1 Cancel Order (dibatalkan)');
    $this->command->info('');
  }
}
