<?php

use App\Models\Setting;

return [
    'roles' => [
        'users' => ['read', 'create', 'update', 'delete'],
        'roles' => ['read', 'create', 'update', 'delete'],
        'categories' => ['read', 'create', 'update', 'delete'],
        'brands' => ['read', 'create', 'update', 'delete'],
        'cities' => ['read', 'create', 'update', 'delete'],
        'governorates' => ['read', 'create', 'update', 'delete'],
        'branchs' => ['read', 'create', 'update', 'delete'],
        'units' => ['read', 'create', 'update', 'delete'],
        'products' => ['read', 'create', 'update', 'delete', 'openStock', 'history', 'show','bulk-edit','show-sell-price','show-purchase-price','import','import-open-stock'],
        'contacts' => ['read', 'create', 'update', 'delete', 'pay','view-payment-history','import'],
        'accounts' => ['read', 'create', 'update', 'delete', 'add-deposit', 'transfer-money','show-transaction-history', 'change-status'],
        'sells' => ['read', 'create', 'update', 'delete', 'update_price','change-delivery-status'],
        'sales-segments' => ['read', 'create', 'update', 'delete'],
        'purchases' => ['read', 'create', 'update', 'delete', 'show','pay','change-delivery-status'],
        'expense-categories' => ['read', 'create', 'update', 'delete'],
        'expenses' => ['read', 'create', 'update', 'delete'],
        'stock-transfers' => ['read', 'create', 'update', 'delete'],
        'spoiled-stock' => ['read', 'create', 'update', 'delete'],
        'sell-return' => ['read', 'create', 'update', 'delete'],
        'purchase-return' => ['read', 'create', 'update', 'delete', 'print-purchase-return'],
        'activity-logs' => ['read'],
      'statistics' => [
        'read-total_sales',
        'read-total_sales_returns',
        'read-total_unpaid_sales',
        'read-total_paid_sales',
        'read-total_purchase', 
        'read-total_paid_purchase',
        'read-total_purchase_returns',  
        'read-total_unpaid_purchase',  
        'read-total_expenses',
        'read-total_profit',
        'read-net_profit',
        'read-AlertedProducts',
            'read-chart',
            'read-total_partial_purchase',
            'read-total_partial_sell',
        ],
        'reports' => [
            'read-change-in-price-report',
            'read-popular-products-report',
            'read-spoiled-products-report',
            'read-dept-report',
            'read-expenses-report',
            'read-transaction-sell-report',
            'read-stock-report',
            'read-reports',
            'read-profit-and-loss-report',
        ]
    ],
];
?>
