<?php

namespace App\Services;

use App\Mail\NewOrderFarmerAlertMail;
use App\Mail\OrderPlacedCustomerMail;
use App\Mail\OrderStatusUpdateAdminMail;
use App\Mail\OrderStatusUpdateCustomerMail;
use App\Models\Order;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class OrderNotificationService
{
    /**
     * Notify Customer, Farmer, and Admin when a new order is placed.
     */
    public static function notifyOrderPlaced(Order $order): void
    {
        $order->loadMissing(['customer', 'farmer.user', 'farmer.market', 'items.product']);

        // 1. Customer Pre-Order Confirmation Email
        try {
            $customerEmail = $order->customer?->email;
            if ($customerEmail) {
                Mail::to($customerEmail)->send(new OrderPlacedCustomerMail($order));
            }
        } catch (\Throwable $e) {
            Log::info("Customer order confirmation email deferred: " . $e->getMessage());
        }

        // 2. Farmer Harvest Alert Email
        try {
            $farmerUserEmail = $order->farmer?->user?->email;
            if ($farmerUserEmail) {
                Mail::to($farmerUserEmail)->send(new NewOrderFarmerAlertMail($order));
            }
        } catch (\Throwable $e) {
            Log::info("Farmer order alert email deferred: " . $e->getMessage());
        }

        // 3. Admin Notification Email
        try {
            $adminEmails = self::getAdminEmails();
            if (!empty($adminEmails)) {
                Mail::to($adminEmails)->send(new OrderStatusUpdateAdminMail($order, 'placed'));
            }
        } catch (\Throwable $e) {
            Log::info("Admin order placed email deferred: " . $e->getMessage());
        }
    }

    /**
     * Notify Customer and Admin when an order's status changes.
     * Supported statuses: accepted, ready_for_pickup, completed, declined, cancelled, etc.
     */
    public static function notifyStatusChange(Order $order, string $status, ?string $reason = null): void
    {
        $order->loadMissing(['customer', 'farmer.user', 'farmer.market', 'items.product']);

        // 1. Customer Status Update Email
        try {
            $customerEmail = $order->customer?->email;
            if ($customerEmail) {
                Mail::to($customerEmail)->send(new OrderStatusUpdateCustomerMail($order, $status, $reason));
            }
        } catch (\Throwable $e) {
            Log::info("Customer order status update email deferred: " . $e->getMessage());
        }

        // 2. Admin Status Update Email
        try {
            $adminEmails = self::getAdminEmails();
            if (!empty($adminEmails)) {
                Mail::to($adminEmails)->send(new OrderStatusUpdateAdminMail($order, $status, $reason));
            }
        } catch (\Throwable $e) {
            Log::info("Admin order status update email deferred: " . $e->getMessage());
        }
    }

    /**
     * Retrieve deliverable admin emails.
     */
    public static function getAdminEmails(): array
    {
        $primaryAdmin = config('mail.admin_address') ?: config('mail.from.address');
        
        $dbAdmins = User::where('role', 'admin')->pluck('email')->toArray();
        
        // Filter out dummy/unresolvable domains like .local that fail live SMTP
        $validDbAdmins = array_filter($dbAdmins, function ($email) {
            return filter_var($email, FILTER_VALIDATE_EMAIL) && !str_ends_with(strtolower($email), '.local');
        });

        $recipients = array_unique(array_filter(array_merge([$primaryAdmin], $validDbAdmins)));

        return !empty($recipients) ? array_values($recipients) : array_filter([$primaryAdmin ?: 'admin@marketlink.com']);
    }
}
