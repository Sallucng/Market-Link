# 🌱 MarketLink — Farm Fresh Just a Click Away

[![Laravel](https://img.shields.io/badge/Laravel-11.x-FF2D20?style=for-the-badge&logo=laravel&logoColor=white)](https://laravel.com)
[![PHP](https://img.shields.io/badge/PHP-8.2%2B-777BB4?style=for-the-badge&logo=php&logoColor=white)](https://php.net)
[![MySQL](https://img.shields.io/badge/MySQL-8.0%20%2F%20XAMPP-4479A1?style=for-the-badge&logo=mysql&logoColor=white)](https://mysql.com)
[![Vite](https://img.shields.io/badge/Vite-Bundled-646CFF?style=for-the-badge&logo=vite&logoColor=white)](https://vitejs.dev)
[![Leaflet](https://img.shields.io/badge/OpenStreetMap-Leaflet-199900?style=for-the-badge&logo=openstreetmap&logoColor=white)](https://leafletjs.com)
[![Google OAuth](https://img.shields.io/badge/Google_OAuth-2.0-4285F4?style=for-the-badge&logo=google&logoColor=white)](https://developers.google.com/identity)
[![License: MIT](https://img.shields.io/badge/License-MIT-yellow.svg?style=for-the-badge)](LICENSE)

> **MarketLink** is an end-to-end farm-to-consumer web platform designed for local weekend farmers' markets. It empowers shoppers to pre-order fresh produce directly from local growers for weekend stall pickup, eliminating platform cuts, shipping delays, and food waste.

---

## 📌 Table of Contents
1. [Key Features & Highlights](#-key-features--highlights)
2. [Tech Stack](#-tech-stack)
3. [Prerequisites](#-prerequisites)
4. [Step-by-Step Local Setup Guide](#-step-by-step-local-setup-guide)
5. [Configuring XAMPP MySQL](#-configuring-xampp-mysql)
6. [Pre-Seeded Demo Accounts](#-pre-seeded-demo-accounts)
7. [Google OAuth & Security Configuration](#-google-oauth--security-configuration)
8. [🚀 100% Free Live Link Deployment Options](#-100-free-live-link-deployment-options)
9. [Troubleshooting & FAQs](#-troubleshooting--faqs)
10. [License & Acknowledgements](#-license--acknowledgements)

---

## 🌟 Key Features & Highlights

- **🗺️ Interactive OpenStreetMap Stall Discovery**: Explore weekend farmers' markets with interactive Leaflet.js map markers, stall directions, and vendor listings.
- **⚡ Live Real-Time Search & Smart Filtering**: Debounced instant search across produce names, categories (Vegetables, Fruits, Dairy, Honey, Baked Goods), and harvest badges.
- **⏰ 2-Hour Market Cutoff Engine**: Automated reservation gating prevents orders within 2 hours of stall closing, ensuring farmers have prep time before market morning.
- **🔐 Dual Authentication & Google OAuth 2.0**:
  - Traditional password login and registration with cryptographic password reset links.
  - One-click **Sign in / Register with Google** with dynamic modal to collect required phone numbers and pickup addresses.
  - Strict role-based isolation (Admin accounts cannot authenticate through public Google OAuth).
- **💳 Flexible Checkout**: In-person zero-fee **Cash on Pickup** or online **Stripe Credit Card** reservation.
- **👨‍🌾 Farmer Stall Management Portal**:
  - Live incoming order queue (`Pending` $\rightarrow$ `Confirmed` $\rightarrow$ `Ready` $\rightarrow$ `Picked Up`).
  - Real-time inventory decrement and restock controls.
  - Stall profile configurations (operating hours, stall numbers, market selection).
- **👤 Customer Hub**: Track active pickups, view stall pickup QR receipts, browse order history, and leave grower reviews.
- **🛡️ Administrator Control Center**: Vendor approval and suspension gates, platform statistics, customer moderation, and system audit logs.
- **🌐 Multilingual & RTL Support**: Native instant localization for **English**, **Spanish (Español)**, and **Urdu (اردو)** with complete Right-to-Left (RTL) layout switching.

---

## 🛠️ Tech Stack

- **Backend Framework:** Laravel 11.x (PHP 8.2+)
- **Database:** MySQL 8.0+ / MariaDB (configured via XAMPP)
- **Frontend & Styling:** Blade Templates, Bootstrap 5, Custom CSS Design Tokens, Vite
- **Mapping & Geolocation:** Leaflet.js + OpenStreetMap (100% free, no API keys needed)
- **Authentication:** Laravel Session Guard + Laravel Socialite (Google OAuth 2.0)
- **Payments:** Stripe Elements (Test mode ready) + Cash on Stall Pickup
- **Icons & UI:** Bootstrap Icons, Lucide Icons

---

## 📋 Prerequisites

Ensure the following tools are installed on your computer:

| Tool | Recommended Version | Download Link |
|---|---|---|
| **PHP** | `8.2` or higher | [php.net](https://www.php.net/downloads) or via XAMPP |
| **Composer** | `2.x` | [getcomposer.org](https://getcomposer.org/download/) |
| **Node.js & npm** | `18.x` or `20.x` LTS | [nodejs.org](https://nodejs.org/) |
| **XAMPP** | `8.2+` (with MySQL) | [apachefriends.org](https://www.apachefriends.org/) |
| **Git** | `2.x` | [git-scm.com](https://git-scm.com/) |

> **PHP Extension Checklist:** Ensure `pdo_mysql`, `curl`, `mbstring`, `fileinfo`, `gd`, and `openssl` are enabled in your `php.ini`.

---

## 💻 Step-by-Step Local Setup Guide

Follow these simple steps to run MarketLink locally on **any computer** (Windows, macOS, or Linux):

### 1. Clone the Repository
```bash
git clone https://github.com/Sallucng/Market-Link.git
cd Market-Link
```

### 2. Install PHP Dependencies
```bash
composer install
```

### 3. Install Frontend Dependencies & Compile Assets
```bash
npm install
npm run build
```
*(For active frontend development, you can instead run `npm run dev` in a separate terminal).*

### 4. Create Your Environment File
Duplicate the template configuration:
```bash
# On Windows (PowerShell / CMD)
copy .env.example .env

# On macOS / Linux
cp .env.example .env
```

Generate your unique Laravel application key:
```bash
php artisan key:generate
```

---

## 🗄️ Configuring XAMPP MySQL

### Step 1: Start MySQL in XAMPP
1. Open the **XAMPP Control Panel**.
2. Click the **Start** button next to **MySQL** (the label turns green and displays Port `3306`).
*(Apache is optional—only start Apache if you want to inspect tables in phpMyAdmin at `http://localhost/phpmyadmin`).*

### Step 2: Verify Your `.env` Database Credentials
Open your `.env` file and confirm the database settings match your local XAMPP setup:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=marketlink
DB_USERNAME=root
DB_PASSWORD=
```

### Step 3: Run Database Migrations & Seed Sample Data
Execute the migration command to automatically create the database (if not existing), migrate all 32 normalized tables, and populate initial test data:
```bash
php artisan migrate:fresh --seed
```

### Step 4: Create the Storage Symlink
Link the public storage folder for user-uploaded product and stall images:
```bash
php artisan storage:link
```

### Step 5: Start the Laravel Web Server
```bash
php artisan serve
```

Visit the application in your browser:
👉 **[http://localhost:8000](http://localhost:8000)** (or `http://127.0.0.1:8000`)

---

## 🔑 Pre-Seeded Demo Accounts

The database comes populated with verified accounts for testing every user role:

| Role | Username / Email | Password | Access & Purpose |
|---|---|---|---|
| **System Admin** | `admin` or `admin@marketlink.com` | `Admin123!` *(or `Admin@123`)* | Full platform governance, vendor verification, audit logs. |
| **Approved Farmer** | `farmer.john` or `farmer.john@marketlink.com` *(or `farmer`)* | `Farmer123!` *(or `Farmer@123`)* | Active stall vendor (*Sunrise Organic Acres*), live inventory, orders. |
| **Secondary Farmer** | `maria.orchards` or `maria.orchards@marketlink.com` | `Farmer123!` *(or `Farmer@123`)* | Active vendor (*Golden Valley Orchards*) with stone fruits & apples. |
| **Customer** | `customer.alice` or `customer.alice@marketlink.com` *(or `customer`)* | `Customer123!` *(or `Customer@123`)* | Local shopper with active and completed pre-orders, saved favorites. |
| **Secondary Customer** | `customer.bob` or `customer.bob@marketlink.com` | `Customer123!` *(or `Customer@123`)* | Additional shopper for concurrent reservation testing. |

---

## 🔒 Google OAuth & Security Configuration

### Setting Up Google Sign-In
To enable Google Login / Registration:
1. Create a project in the [Google Cloud Console](https://console.cloud.google.com/apis/credentials).
2. Create an **OAuth 2.0 Client ID** (Web Application).
3. Set Authorized Redirect URI:
   ```
   http://127.0.0.1:8000/auth/google/callback
   ```
4. Add the credentials to your `.env` file:
   ```env
   GOOGLE_CLIENT_ID=your-google-client-id.apps.googleusercontent.com
   GOOGLE_CLIENT_SECRET=your-google-client-secret
   GOOGLE_REDIRECT_URI="${APP_URL}/auth/google/callback"
   ```

### 💡 Windows cURL SSL Certificate Fix (If encountering cURL error 60):
If PHP on Windows throws `cURL error 60: SSL certificate problem: unable to get local issuer certificate`:
1. Download the latest CA bundle from Mozilla: [cacert.pem](https://curl.se/ca/cacert.pem).
2. Save it to your PHP directory (e.g., `C:\php\extras\ssl\cacert.pem` or `C:\xampp\php\extras\ssl\cacert.pem`).
3. Open `php.ini` and set:
   ```ini
   curl.cainfo = "C:\xampp\php\extras\ssl\cacert.pem"
   openssl.cafile = "C:\xampp\php\extras\ssl\cacert.pem"
   ```
4. Restart your terminal / server.

---

## 🚀 100% Free Live Link Deployment Options

You can deploy MarketLink to a public, shareable live link **100% for free** without paying for hosting. Below are the best methods:

---

### Option 1: Instant Public Live Link via Cloudflare Tunnels (⭐ Recommended for Quick Demos)
*Host directly from your computer with zero server costs, zero port-forwarding, and a valid SSL `https://` domain.*

1. Download the free [Cloudflare Tunnel (cloudflared)](https://developers.cloudflare.com/cloudflare-one/connections/connect-networks/downloads/):
   - **Windows:** `winget install --id Cloudflare.cloudflared`
   - **macOS:** `brew install cloudflare/cloudflare/cloudflared`
   - **Linux:** `sudo apt-get install cloudflared`
2. With XAMPP MySQL and `php artisan serve` running on port 8000, run:
   ```bash
   cloudflared tunnel --url http://localhost:8000
   ```
3. Cloudflare will instantly output a public URL like:
   ```
   https://random-assigned-name.trycloudflare.com
   ```
4. Update `APP_URL` in `.env` to your tunnel URL if you want Google OAuth to redirect properly.
5. Anyone in the world can now open and use your live MarketLink website!

---

### Option 2: Instant Public Live Link via ngrok
1. Sign up for a free account at [ngrok.com](https://ngrok.com).
2. Download ngrok and authenticate:
   ```bash
   ngrok config add-authtoken <YOUR_TOKEN>
   ```
3. Start the tunnel:
   ```bash
   ngrok http 8000
   ```
4. Share the generated `https://xxxx.ngrok-free.app` URL with anyone!

---

### Option 3: 24/7 Cloud Hosting on Render + Free TiDB Serverless MySQL (⭐ Recommended 100% Free Forever)
*If you need MarketLink to stay online 24/7 permanently without keeping your computer on:*

1. **Step 1: Get Your Free Cloud MySQL (60 Seconds, 5 GB Free Forever)**:
   - Go to **[TiDB Cloud (Serverless)](https://tidbcloud.com)** (100% free, no credit card required).
   - Click **Create Cluster** $\rightarrow$ select **Serverless (Free)**.
   - Click **Connect** $\rightarrow$ select **General** (or MySQL CLI) to reveal your:
     - `Host` (e.g. `gateway01.us-east-1.prod.aws.tidbcloud.com`)
     - `Port` (`4000` or `3306`)
     - `Database` (`test` or create `marketlink`)
     - `User` (e.g. `xxxxxx.root`)
     - `Password`

2. **Step 2: Deploy to Render**:
   - Go to **[dashboard.render.com](https://dashboard.render.com)**.
   - Click **New +** $\rightarrow$ **Web Service**.
   - Connect your GitHub repository `Sallucng/Market-Link`.
   - Render automatically detects the production [`Dockerfile`](file:///c:/Users/User/Desktop/Projects/Websites/MarketLink/Dockerfile) and [`render.yaml`](file:///c:/Users/User/Desktop/Projects/Websites/MarketLink/render.yaml)!
   - Under **Environment Variables**, add:
     | Key | Value |
     |---|---|
     | `APP_KEY` | *(Generate in Render or copy from your local `.env`)* |
     | `APP_URL` | `https://your-service-name.onrender.com` |
     | `DB_CONNECTION` | `mysql` |
     | `DB_HOST` | *Your TiDB Host* |
     | `DB_PORT` | `4000` *(or your TiDB port)* |
     | `DB_DATABASE` | *Your TiDB Database name* |
     | `DB_USERNAME` | *Your TiDB User* |
     | `DB_PASSWORD` | *Your TiDB Password* |
     | `RUN_SEEDER` | `true` *(Automatically seeds demo users & products on first deploy)* |

3. **Step 3: Click Deploy Web Service**:
   - Render builds the production Docker container, runs migrations, seeds initial data, and assigns a permanent SSL URL like:
     ```
     https://marketlink.onrender.com
     ```
   - It is permanently live 24/7 at **$0.00/month**!

---

### Option 4: Koyeb (Free Nano Instance)
1. Sign up for [koyeb.com](https://www.koyeb.com) (includes 1 free web service).
2. Connect your GitHub repository.
3. Koyeb automatically builds the Laravel application and provisions a free global HTTPS endpoint.

---

## ❓ Troubleshooting & FAQs

### Q: Port 3306 is already in use when starting MySQL in XAMPP!
- **Fix:** Another MySQL or MariaDB instance is likely running. Open Windows Services (`services.msc`), find `MySQL` or `MariaDB`, and click **Stop**. Alternatively, edit `C:\xampp\mysql\bin\my.ini` to change the port to `3307` and update `DB_PORT=3307` in your `.env`.

### Q: Vite manifest not found error (`Vite manifest not found at: public/build/manifest.json`)
- **Fix:** Run `npm run build` from your project root. This creates the production bundles in `public/build`.

### Q: Uploaded product or stall images are not displaying
- **Fix:** Ensure the symbolic link is created:
  ```bash
  php artisan storage:link
  ```

### Q: How do I test password reset emails locally?
- **Fix:** In your `.env`, set `MAIL_MAILER=log`. When you request a password reset from `/forgot-password`, the signed reset link is written directly to `storage/logs/laravel.log`. Open that log file, copy the reset link, and paste it into your browser.

---

## 📄 License & Acknowledgements

- **License:** Open-source software licensed under the [MIT License](LICENSE).
- **Cartography:** Map data © [OpenStreetMap](https://www.openstreetmap.org/) contributors, rendered via [Leaflet.js](https://leafletjs.com/).
- **Icons:** [Bootstrap Icons](https://icons.getbootstrap.com/).
