# 🌾 MarketLink — Developer & Agent Specification for Person 2
**Theme:** eGreen Basket | **Category:** End-to-End Web Solutions  
**Document Reference:** MarketLink SRS v1.0  
**Stack:** PHP (Laravel) + MySQL + Blade Templates + Bootstrap 5 + JavaScript  
**Target Architecture:** Multi-tier MVC Monolith (Evaluator-friendly via XAMPP / `php artisan serve`)

---

## 1. Project Background & System Context (For Your AI Agent)

MarketLink (*"Farm Fresh Just a Click Away"*) is an end-to-end web platform connecting local farmers-market vendors with community customers. Customers discover which farmers will attend which markets, check weekly stock, and place pre-orders for in-person stall pickup. Farmers manage recurring weekly stock, set cutoff times, and process incoming pre-orders.

### ⚠️ STRICT SRS BOUNDARIES (Zero Scope Creep)
Your AI agent must follow these non-negotiable boundaries from SRS Section 1.5:
1. **NO Online Payment Gateways:** All orders are pre-orders. Payment is strictly settled **in person at pickup** (cash/stall payment). Do not integrate Stripe, PayPal, or card gateways.
2. **NO Delivery / Courier Logistics:** Pre-orders are strictly **pickup-only** at market stalls. No shipping addresses, couriers, or shipping fees.
3. **NO Licensing / Certification Verification:** Farmer identity/organic verification is explicitly out of scope.
4. **Approval Gate:** Farmers **cannot list stock or accept orders** until an Admin approves their registration.

---

## 2. Team Division & File Isolation Protocol

This project is divided between **Person 1** and **Person 2**. Both developers are using AI coding agents.  
To prevent **Git merge conflicts**, file ownership is strictly segregated:

| Domain | Owner | Managed Files / Namespaces |
|---|---|---|
| **Base Setup & Shared Migrations** | Person 1 | `database/migrations/`, `app/Models/`, base layout `resources/views/layouts/app.blade.php` |
| **Customer & Public Pages** | Person 1 | `app/Http/Controllers/Customer/`, `resources/views/customer/`, `resources/views/public/`, Leaflet Maps, Pre-order checkout |
| **Farmer / Vendor Portal** | **Person 2 (YOU)** | `app/Http/Controllers/Farmer/`, `resources/views/farmer/`, `routes/farmer.php` |
| **Admin Backoffice & Reports** | **Person 2 (YOU)** | `app/Http/Controllers/Admin/`, `resources/views/admin/`, `routes/admin.php` |
| **Deliverables & SQL Export** | **Person 2 (YOU)** | `marketlink.sql`, `ReadMe.doc` installation guide, test credentials |

> 💡 **CRITICAL FOR YOUR AI:** Only create or edit files in your assigned directories (`app/Http/Controllers/Farmer/`, `app/Http/Controllers/Admin/`, `resources/views/farmer/`, `resources/views/admin/`, and your route files). Do not modify Person 1's customer controllers or views.

---

## 3. Shared Database Schema Reference

Use these exact table and column names to ensure 100% interoperability with Person 1:

- **`users`:** `id`, `username`, `email`, `password`, `full_name`, `contact_number`, `address`, `role` (`admin`, `farmer`, `customer`), `is_active` (bool), `is_approved` (bool), `created_at`, `updated_at`.
- **`markets`:** `id`, `name`, `address`, `city`, `operating_days`, `timings`, `latitude`, `longitude`, `map_provider`, `created_at`, `updated_at`.
- **`farmers`:** `id`, `user_id` (FK to users), `market_id` (FK to markets, nullable), `stall_name`, `contact_person`, `contact_number`, `address`, `latitude`, `longitude`, `operating_days`, `pickup_time_windows` (text/json), `cutoff_hours` (integer, default 2), `bio`, `image_url`.
- **`categories`:** `id`, `name`, `description`.
- **`products`:** `id`, `farmer_id` (FK to farmers), `category_id` (FK to categories), `name`, `description`, `price`, `unit` (kg, bunch, box, etc.), `stock_quantity`, `weekly_recurring_stock`, `is_sold_out` (bool), `is_available` (bool), `image_url`.
- **`orders`:** `id`, `customer_id` (FK to users), `farmer_id` (FK to farmers), `market_id` (FK to markets), `order_status` (`placed`, `accepted`, `ready_for_pickup`, `completed`, `cancelled`, `declined`), `pickup_date`, `pickup_time_slot`, `total_amount`, `cutoff_time`, `notes`, `created_at`.
- **`order_items`:** `id`, `order_id` (FK to orders), `product_id` (FK to products), `quantity`, `unit_price`, `subtotal`.
- **`reviews`:** `id`, `order_id` (FK to orders), `product_id` (FK to products, nullable), `farmer_id` (FK to farmers), `customer_id` (FK to users), `rating` (1-5), `comment`, `farmer_response`, `created_at`.
- **`announcements`:** `id`, `title`, `content`, `created_by` (FK to users), `created_at`.
- **`notifications`:** `id`, `user_id` (FK to users), `title`, `message`, `is_read`, `type`, `created_at`.

---

## 4. Your Detailed Functional Requirements Checklist

### PART A: Farmer / Vendor Portal (`/farmer/*`)
*Route group prefix: `/farmer`, protected by `auth` and `role:farmer` middleware.*

1. **Farmer Dashboard (`/farmer/dashboard`):**
   - KPI Summary Cards: Total Orders, Pending Orders, Today's Pickups, Total Revenue Summary (settled at pickup).
   - Table of Recent Pre-Orders with quick status updates.
   - Best-selling products list.

2. **Farmer Stall Profile Management (`/farmer/profile`):**
   - Manage Stall Name, Contact Person, Contact Phone, Stall Address.
   - Market selector (choose which market the stall belongs to).
   - Operating Days (e.g., Saturday, Sunday).
   - Pickup Time Windows (e.g., "08:00 AM - 10:00 AM", "10:30 AM - 12:30 PM").
   - Order Cutoff Configuration: Number of hours before pickup window when pre-orders lock (e.g., 2 hours).
   - Geolocation coordinates: Latitude & Longitude for map marker display.

3. **Weekly Stock & Pricing Management (`/farmer/products`):**
   - **Product CRUD:** List, Add, Edit, Delete products with Name, Category, Price, Unit (kg/bunch/dozen), Available Quantity, Description, and Image.
   - **Weekly Stock Template:** Button/form to define a default weekly stock template and 1-click "Reset to Weekly Template" to replenish inventory for a new market day.
   - **Availability Toggles:** Instant toggles for "Mark as Sold Out" and "Mark as Temporarily Unavailable".

4. **Pre-Order Queue Management (`/farmer/orders`):**
   - View list of incoming customer pre-orders with customer name, phone, pickup date, pickup slot, items, and total amount (to be paid in person).
   - **Status Workflow Actions:**
     - `Accept`: Farmer confirms availability.
     - `Decline`: Farmer rejects order if stock is unavailable.
     - `Mark Ready for Pickup`: Changes order status to `ready_for_pickup` and generates an in-app customer notification.
     - `Mark Completed`: Farmer marks order completed when customer arrives at the stall and settles payment.

5. **Customer Review Responses (`/farmer/reviews`):**
   - View customer star ratings and written reviews.
   - Provide a text reply / response to customer feedback.

---

### PART B: Administrator Portal (`/admin/*`)
*Route group prefix: `/admin`, protected by `auth` and `role:admin` middleware.*

1. **Admin Dashboard (`/admin/dashboard`):**
   - High-level platform KPIs: Total Farmers, Total Customers, Total Markets, Total Orders, Total Platform Volume.
   - Quick overview of pending approvals.

2. **Farmer Approval Gate (`/admin/farmers`):**
   - View all registered farmers with approval status (`pending`, `approved`, `suspended`).
   - One-click buttons to **Approve** or **Suspend** farmers.
   - *Rule:* If `is_approved == false`, the farmer's products must NOT appear in the customer public catalog.

3. **Customer Account Moderation (`/admin/customers`):**
   - View registered customer accounts.
   - Toggle customer account status: **Activate** or **Deactivate** (in case of policy violations).

4. **Market Management (`/admin/markets`):**
   - Full CRUD for farmers markets: Market Name, Address, City, Operating Days, Timings, Latitude, Longitude, and Map Provider.

5. **Content Moderation (`/admin/moderation`):**
   - View and moderate product listings across all farmers (remove inappropriate listings).
   - View and remove abusive or inappropriate customer reviews.

6. **Platform Reports & Analytics (`/admin/reports`):**
   - Total Orders report (filterable by date range).
   - Revenue summary across different markets.
   - Most active farmers report (ranked by completed orders and revenue).
   - Printable / exportable clean table view.

7. **System Master Data & Announcements (`/admin/system`):**
   - CRUD for Product Categories (Vegetables, Fruits, Dairy, Baked Goods, etc.).
   - Broadcast platform-wide announcements (displayed on the customer landing page).

---

### PART C: Mandatory Competition Deliverables (SRS §1.9)
Your AI agent must prepare these submission assets:
1. **SQL Database Script (`marketlink.sql`):**
   - Clean MySQL dump containing `CREATE TABLE` statements and sample seed data.
2. **Pre-Seeded Test Credentials:**
   Ensure the seed data contains these exact accounts:
   - **Admin:** `admin` / `admin@marketlink.local` / `Admin@123`
   - **Approved Farmer:** `greenvalley` / `farmer@marketlink.local` / `Farmer@123`
   - **Pending Farmer:** `newharvest` / `newharvest@marketlink.local` / `Farmer@123`
   - **Customer:** `sarah_shopper` / `customer@marketlink.local` / `Customer@123`
3. **Installation Manual (`ReadMe.doc` / `README.md`):**
   - Exact steps for evaluators to import `marketlink.sql` via phpMyAdmin, configure `.env`, run `php artisan serve`, and log in with the test accounts.

---

## 5. Instructions for Your AI Agent
1. **Pull the repository** after Person 1 sets up the initial Laravel skeleton and database migrations.
2. Create your feature branch: `git checkout -b feature/farmer-admin-portal`.
3. Build the controllers in `app/Http/Controllers/Farmer/` and `app/Http/Controllers/Admin/`.
4. Build the Blade views in `resources/views/farmer/` and `resources/views/admin/` using **Bootstrap 5** cards, tables, badges, and modals.
5. Create your routes in `routes/farmer.php` and `routes/admin.php`, then include them in `routes/web.php`.
6. Test every feature against the checklist above.
