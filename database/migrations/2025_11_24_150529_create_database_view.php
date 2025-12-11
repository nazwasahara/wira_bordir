<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // ====================================
        // VIEW 1: Complete Order Details
        // ====================================
        DB::statement("
            CREATE OR REPLACE VIEW view_order_details AS
            SELECT 
                o.id AS order_id,
                o.customer_name,
                o.customer_phone_number,
                o.customer_address,
                o.total_price,
                o.amount_paid,
                o.payment_proof,
                o.status AS order_status,
                o.created_at AS order_date,
                o.updated_at AS order_updated,
                
                u.id AS user_id,
                u.username AS user_username,
                u.email AS user_email,
                u.is_active AS user_active,
                
                (SELECT COUNT(*) FROM order_items WHERE order_id = o.id) AS total_items,
                (SELECT SUM(quantity) FROM order_items WHERE order_id = o.id) AS total_quantity,
                
                (o.total_price - o.amount_paid) AS remaining_payment,
                CASE 
                    WHEN o.amount_paid >= o.total_price THEN 'LUNAS'
                    WHEN o.amount_paid > 0 THEN 'PARTIAL'
                    ELSE 'UNPAID'
                END AS payment_status,
                
                ct.cancellation_date,
                ct.cancellation_reason
                
            FROM orders o
            LEFT JOIN users u ON o.user_id = u.id
            LEFT JOIN cancelled_transactions ct ON o.id = ct.order_id
        ");

        // ====================================
        // VIEW 2: Order Items with Product Details
        // ====================================
        DB::statement("
            CREATE OR REPLACE VIEW view_order_items_details AS
            SELECT 
                oi.id AS order_item_id,
                oi.order_id,
                oi.quantity,
                oi.final_price,
                (oi.quantity * oi.final_price) AS subtotal,
                
                o.customer_name,
                o.status AS order_status,
                o.created_at AS order_date,
                
                p.id AS product_id,
                p.product_name,
                p.base_price AS product_base_price,
                
                m.id AS material_id,
                m.name AS material_name,
                m.price AS material_price,
                
                mc.id AS material_color_id,
                mc.name AS material_color_name,
                mc.price AS material_color_price,
                
                st.id AS sash_type_id,
                st.name AS sash_type_name,
                st.price AS sash_type_price,
                
                f.id AS font_id,
                f.name AS font_name,
                f.price AS font_price,
                
                sm.id AS side_motif_id,
                sm.name AS side_motif_name,
                sm.price AS side_motif_price,
                
                rc.id AS ribbon_color_id,
                rc.name AS ribbon_color_name,
                rc.price AS ribbon_color_price,
                
                lo.id AS lace_option_id,
                lo.color AS lace_color,
                lo.size AS lace_size,
                lo.price AS lace_price,
                
                ro.id AS rombe_option_id,
                ro.color AS rombe_color,
                ro.size AS rombe_size,
                ro.price AS rombe_price,
                
                mro.id AS motif_ribbon_option_id,
                mro.color AS motif_ribbon_color,
                mro.size AS motif_ribbon_size,
                mro.price AS motif_ribbon_price,
                
                aio.id AS additional_item_option_id,
                aio.color AS additional_item_color,
                aio.model AS additional_item_model,
                aio.price AS additional_item_price,
                ai.name AS additional_item_name,
                
                COALESCE(p.base_price, 0) + 
                COALESCE(m.price, 0) + 
                COALESCE(mc.price, 0) + 
                COALESCE(st.price, 0) + 
                COALESCE(f.price, 0) + 
                COALESCE(sm.price, 0) + 
                COALESCE(rc.price, 0) + 
                COALESCE(lo.price, 0) + 
                COALESCE(ro.price, 0) + 
                COALESCE(mro.price, 0) + 
                COALESCE(aio.price, 0) AS calculated_price
                
            FROM order_items oi
            JOIN orders o ON oi.order_id = o.id
            JOIN products p ON oi.product_id = p.id
            LEFT JOIN materials m ON oi.material_id = m.id
            LEFT JOIN material_colors mc ON oi.material_color_id = mc.id
            LEFT JOIN sash_types st ON oi.sash_type_id = st.id
            LEFT JOIN fonts f ON oi.font_id = f.id
            LEFT JOIN side_motifs sm ON oi.side_motif_id = sm.id
            LEFT JOIN ribbon_colors rc ON oi.ribbon_color_id = rc.id
            LEFT JOIN lace_options lo ON oi.lace_option_id = lo.id
            LEFT JOIN rombe_options ro ON oi.rombe_option_id = ro.id
            LEFT JOIN motif_ribbon_options mro ON oi.motif_ribbon_option_id = mro.id
            LEFT JOIN additional_item_options aio ON oi.additional_item_option_id = aio.id
            LEFT JOIN additional_items ai ON aio.additional_item_id = ai.id
        ");

        // ====================================
        // VIEW 3: Sales Summary by Product
        // ====================================
        DB::statement("
            CREATE OR REPLACE VIEW view_sales_by_product AS
            SELECT 
                p.id AS product_id,
                p.product_name,
                p.base_price,
                COUNT(DISTINCT oi.order_id) AS total_orders,
                SUM(oi.quantity) AS total_quantity_sold,
                SUM(oi.quantity * oi.final_price) AS total_revenue,
                AVG(oi.final_price) AS average_price,
                MIN(o.created_at) AS first_order_date,
                MAX(o.created_at) AS last_order_date
            FROM products p
            LEFT JOIN order_items oi ON p.id = oi.product_id
            LEFT JOIN orders o ON oi.order_id = o.id AND o.status = 'done'
            GROUP BY p.id, p.product_name, p.base_price
        ");

        // ====================================
        // VIEW 4: Customer Statistics
        // ====================================
        DB::statement("
            CREATE OR REPLACE VIEW view_customer_statistics AS
            SELECT 
                u.id AS customer_id,
                u.username,
                u.email,
                u.phone_number,
                u.is_active,
                u.created_at AS registered_date,
                
                COUNT(o.id) AS total_orders,
                SUM(CASE WHEN o.status = 'done' THEN 1 ELSE 0 END) AS completed_orders,
                SUM(CASE WHEN o.status = 'cancel' THEN 1 ELSE 0 END) AS cancelled_orders,
                SUM(CASE WHEN o.status = 'pending' THEN 1 ELSE 0 END) AS pending_orders,
                
                SUM(CASE WHEN o.status = 'done' THEN o.total_price ELSE 0 END) AS total_spent,
                AVG(CASE WHEN o.status = 'done' THEN o.total_price ELSE NULL END) AS average_order_value,
                
                MAX(o.created_at) AS last_order_date,
                DATEDIFF(NOW(), MAX(o.created_at)) AS days_since_last_order
                
            FROM users u
            LEFT JOIN orders o ON u.id = o.user_id
            WHERE u.role = 'customer'
            GROUP BY u.id, u.username, u.email, u.phone_number, u.is_active, u.created_at
        ");

        // ====================================
        // VIEW 5: Daily Sales Report
        // ====================================
        DB::statement("
            CREATE OR REPLACE VIEW view_daily_sales AS
            SELECT 
                DATE(o.created_at) AS sale_date,
                COUNT(o.id) AS total_orders,
                SUM(CASE WHEN o.status = 'done' THEN 1 ELSE 0 END) AS completed_orders,
                SUM(CASE WHEN o.status = 'cancel' THEN 1 ELSE 0 END) AS cancelled_orders,
                SUM(CASE WHEN o.status = 'pending' THEN 1 ELSE 0 END) AS pending_orders,
                SUM(o.total_price) AS total_revenue,
                SUM(o.amount_paid) AS total_paid,
                SUM(CASE WHEN o.status = 'done' THEN o.total_price ELSE 0 END) AS confirmed_revenue,
                AVG(o.total_price) AS average_order_value,
                COUNT(DISTINCT o.user_id) AS unique_customers
            FROM orders o
            GROUP BY DATE(o.created_at)
            ORDER BY DATE(o.created_at) DESC
        ");

        // ====================================
        // VIEW 6: Monthly Sales Report (FIXED)
        // ====================================
        DB::statement("
            CREATE OR REPLACE VIEW view_monthly_sales AS
            SELECT 
                YEAR(o.created_at) AS sale_year,
                MONTH(o.created_at) AS sale_month,
                DATE_FORMAT(o.created_at, '%Y-%m') AS period_month,
                COUNT(o.id) AS total_orders,
                SUM(CASE WHEN o.status = 'done' THEN 1 ELSE 0 END) AS completed_orders,
                SUM(CASE WHEN o.status = 'cancel' THEN 1 ELSE 0 END) AS cancelled_orders,
                SUM(o.total_price) AS total_revenue,
                SUM(CASE WHEN o.status = 'done' THEN o.total_price ELSE 0 END) AS confirmed_revenue,
                AVG(o.total_price) AS average_order_value,
                COUNT(DISTINCT o.user_id) AS unique_customers
            FROM orders o
            GROUP BY sale_year, sale_month, period_month
            ORDER BY sale_year DESC, sale_month DESC
        ");

        // ====================================
        // VIEW 7: Material Colors with Material Info
        // ====================================
        DB::statement("
            CREATE OR REPLACE VIEW view_material_colors_complete AS
            SELECT 
                mc.id AS material_color_id,
                mc.name AS color_name,
                mc.price AS color_price,
                m.id AS material_id,
                m.name AS material_name,
                m.price AS material_price,
                (m.price + mc.price) AS combined_price
            FROM material_colors mc
            JOIN materials m ON mc.material_id = m.id
        ");

        // ====================================
        // VIEW 8: Additional Item Options Complete
        // ====================================
        DB::statement("
            CREATE OR REPLACE VIEW view_additional_item_options_complete AS
            SELECT 
                aio.id AS option_id,
                aio.color,
                aio.model,
                aio.price AS option_price,
                ai.id AS item_id,
                ai.name AS item_name,
                ai.price AS item_price,
                (ai.price + aio.price) AS combined_price
            FROM additional_item_options aio
            JOIN additional_items ai ON aio.additional_item_id = ai.id
        ");

        // ====================================
        // VIEW 9: Order Status Summary
        // ====================================
        DB::statement("
            CREATE OR REPLACE VIEW view_order_status_summary AS
            SELECT 
                o.status,
                COUNT(o.id) AS total_count,
                SUM(o.total_price) AS total_value,
                SUM(o.amount_paid) AS total_paid,
                AVG(o.total_price) AS average_value,
                MIN(o.created_at) AS oldest_order,
                MAX(o.created_at) AS newest_order
            FROM orders o
            GROUP BY o.status
        ");

        // ====================================
        // VIEW 10: Purchase Invoice Details Complete
        // ====================================
        DB::statement("
            CREATE OR REPLACE VIEW view_purchase_invoice_complete AS
            SELECT 
                pi.id AS invoice_id,
                pi.invoice_date,
                pid.id AS detail_id,
                pid.item_type,
                pid.item_id,
                pid.quantity,
                pid.unit_price,
                (pid.quantity * pid.unit_price) AS line_total,
                
                CASE pid.item_type
                    WHEN 'material' THEN (SELECT name FROM materials WHERE id = pid.item_id)
                    WHEN 'product' THEN (SELECT product_name FROM products WHERE id = pid.item_id)
                    WHEN 'additional_item' THEN (SELECT name FROM additional_items WHERE id = pid.item_id)
                    ELSE 'Unknown'
                END AS item_name
                
            FROM purchase_invoices pi
            JOIN purchase_invoice_details pid ON pi.id = pid.invoice_id
        ");

        // ====================================
        // VIEW 11: Top Selling Products
        // ====================================
        DB::statement("
            CREATE OR REPLACE VIEW view_top_selling_products AS
            SELECT 
                p.id AS product_id,
                p.product_name,
                p.base_price,
                COUNT(oi.id) AS times_ordered,
                SUM(oi.quantity) AS total_quantity,
                SUM(oi.quantity * oi.final_price) AS total_revenue,
                AVG(oi.final_price) AS avg_selling_price
            FROM products p
            JOIN order_items oi ON p.id = oi.product_id
            JOIN orders o ON oi.order_id = o.id
            WHERE o.status = 'done'
            GROUP BY p.id, p.product_name, p.base_price
            ORDER BY total_revenue DESC
        ");

        // ====================================
        // VIEW 12: Customization Usage Statistics
        // ====================================
        DB::statement("
            CREATE OR REPLACE VIEW view_customization_usage AS
            SELECT 
                'Material' AS customization_type,
                m.name AS item_name,
                COUNT(oi.id) AS usage_count,
                SUM(oi.quantity) AS total_quantity
            FROM materials m
            JOIN order_items oi ON m.id = oi.material_id
            JOIN orders o ON oi.order_id = o.id
            WHERE o.status = 'done'
            GROUP BY m.id, m.name
            
            UNION ALL
            
            SELECT 
                'Font' AS customization_type,
                f.name AS item_name,
                COUNT(oi.id) AS usage_count,
                SUM(oi.quantity) AS total_quantity
            FROM fonts f
            JOIN order_items oi ON f.id = oi.font_id
            JOIN orders o ON oi.order_id = o.id
            WHERE o.status = 'done'
            GROUP BY f.id, f.name
            
            UNION ALL
            
            SELECT 
                'Sash Type' AS customization_type,
                st.name AS item_name,
                COUNT(oi.id) AS usage_count,
                SUM(oi.quantity) AS total_quantity
            FROM sash_types st
            JOIN order_items oi ON st.id = oi.sash_type_id
            JOIN orders o ON oi.order_id = o.id
            WHERE o.status = 'done'
            GROUP BY st.id, st.name
            
            ORDER BY customization_type, usage_count DESC
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement('DROP VIEW IF EXISTS view_customization_usage');
        DB::statement('DROP VIEW IF EXISTS view_top_selling_products');
        DB::statement('DROP VIEW IF EXISTS view_purchase_invoice_complete');
        DB::statement('DROP VIEW IF EXISTS view_order_status_summary');
        DB::statement('DROP VIEW IF EXISTS view_additional_item_options_complete');
        DB::statement('DROP VIEW IF EXISTS view_material_colors_complete');
        DB::statement('DROP VIEW IF EXISTS view_monthly_sales');
        DB::statement('DROP VIEW IF EXISTS view_daily_sales');
        DB::statement('DROP VIEW IF EXISTS view_customer_statistics');
        DB::statement('DROP VIEW IF EXISTS view_sales_by_product');
        DB::statement('DROP VIEW IF EXISTS view_order_items_details');
        DB::statement('DROP VIEW IF EXISTS view_order_details');
    }
};
