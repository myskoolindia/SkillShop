<?php

namespace App\Commands;

use App\Libraries\LaravelShopOrderSync;
use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

class ShopOrderSyncCommand extends BaseCommand
{
    protected $group = 'Shop';
    protected $name = 'shop:sync';
    protected $description = 'Sync Club Shop orders to Laravel shop_orders';
    protected $usage = 'shop:sync [order_id]';

    public function run(array $params)
    {
        $orderId = $params[0] ?? null;
        if ($orderId) {
            CLI::write("Syncing order ID: {$orderId}...");
            LaravelShopOrderSync::syncOrderSafely((int)$orderId);
            CLI::write("Done.");
            return EXIT_SUCCESS;
        }

        CLI::write("Syncing all orders...");
        $db = \Config\Database::connect();
        $orders = $db->table('orders')->get()->getResult();
        foreach ($orders as $order) {
            LaravelShopOrderSync::syncOrderSafely($order->id);
            CLI::write("Synced order ID: {$order->id} (#{$order->order_number})");
        }
        CLI::write("All orders synced.");
        return EXIT_SUCCESS;
    }
}

