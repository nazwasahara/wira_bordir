<?php

namespace App\Http\View\Composers;

use App\Models\Order;
use Illuminate\View\View;
use Carbon\Carbon;

class OwnerNavbarComposer
{
  /**
   * Bind data to the view. 
   */
  public function compose(View $view): void
  {
    // Get today's sales
    $todaySales = Order::whereDate('created_at', Carbon::today())
      ->whereIn('status', [
        Order::STATUS_PAID,
        Order::STATUS_CONFIRM,
        Order::STATUS_PROCESSING,
        Order::STATUS_DONE,
      ])
      ->sum('amount_paid');

    // Get pending orders count
    $pendingOrders = Order::whereIn('status', [
      Order::STATUS_PENDING,
      Order::STATUS_PAID,
    ])->count();

    // Pass to view
    $view->with([
      'navbar_today_sales' => $todaySales,
      'navbar_pending_orders' => $pendingOrders,
    ]);
  }
}
