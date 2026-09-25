# MarketLink — Farm Fresh Just a Click Away
**Theme:** eGreen Basket  
**Category:** End-to-End Web Solutions  
**SRS Reference:** Software Requirements Specification Version 1.0  

---

## 1. Project Overview & Problem Definition
Local farmers markets connect communities with fresh, seasonal, and locally grown produce. However, shoppers often do not know in advance which farmers will attend, what inventory they will bring, or what prices will be charged. Farmers, in turn, have had no structured way to publicize weekly stock, gauge demand, or take reservations ahead of market morning.

**MarketLink** bridges this gap as an end-to-end web platform providing:
- Real-time weekly stock visibility for local growers.
- Location discovery via interactive OpenStreetMap with markers and pickup directions.
- Pre-order produce reservations for convenient in-person stall collection.
- Zero online transaction fees and no courier logistics — fully adhering to the local market ethos.

---

## 2. Mandatory User Credentials (SRS Section 1.9)

Pre-seeded accounts are configured for immediate evaluation:

| Role | Username | Email | Password | Access Level & Description |
|---|---|---|---|---|
| **System Admin** | `admin` | `admin@marketlink.local` | `Admin@123` | Full administrative oversight, farmer approval gate, customer moderation, market management. |
| **Approved Farmer** | `greenvalley` | `farmer@marketlink.local` | `Farmer@123` | Active stall vendor (Green Valley Organic Produce) with weekly stock, incoming orders, and reviews. |
| **Approved Farmer 2**| `sunshineorchard`| `orchard@marketlink.local` | `Farmer@123`| Active stall vendor (Sunshine Orchards & Apiary) at Riverside Market. |
| **Pending Farmer** | `newharvest` | `newharvest@marketlink.local` | `Farmer@123` | Newly registered vendor awaiting Admin approval (products hidden from public catalog). |
| **Customer** | `sarah_shopper` | `customer@marketlink.local` | `Customer@123` | Pre-loaded customer with active and completed pre-orders, saved favorites, and review history. |
| **Customer 2** | `david_miller` | `david@marketlink.local` | `Customer@123` | Secondary test customer account for multi-user reservation testing. |

---

## 3. Mandatory Installation & Setup Instructions (SRS §1.9)

### Prerequisites:
- **PHP:** 8.2 or higher (with `pdo_sqlite`, `pdo_mysql`, `curl`, `mbstring`, `fileinfo`, `gd`, `openssl` extensions enabled).
- **Composer:** Dependency manager for PHP.
- **MySQL / XAMPP (Optional for MySQL testing):** ApacheFriends XAMPP 8.2+ or standalone MySQL.

---

### Step-by-Step Installation:

#### 1. Clone / Extract Project:
Extract the submitted zip file or clone the repository to your local web directory:
```bash
cd MarketLink
```

#### 2. Install PHP Dependencies:
```bash
composer install
```

#### 3. Environment Configuration:
Copy `.env.example` to `.env` (if not already present):
```bash
copy .env.example .env
php artisan key:generate
```

#### 4. Database Setup (Two Supported Modes):

##### Mode A: Instant SQLite (Zero-Configuration — Recommended for Fast Evaluation)
MarketLink comes configured with a self-contained SQLite database that runs immediately without configuring MySQL services:
```bash
php artisan migrate:fresh --seed
```

##### Mode B: MySQL / phpMyAdmin (XAMPP Standard)
1. Open XAMPP Control Panel and start **Apache** and **MySQL**.
2. Open **phpMyAdmin** (`http://localhost/phpmyadmin`).
3. Create a new database named `marketlink`.
4. Click **Import** and select the provided `marketlink.sql` file in the project root directory.
5. In your `.env` file, update:
   ```env
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=marketlink
   DB_USERNAME=root
   DB_PASSWORD=
   ```

#### 5. Launch the Application:
Start the local development server:
```bash
php artisan serve
```
Open your browser and visit: **`http://localhost:8000`**

---

## 4. Key Architectural Assumptions (SRS Section 1.9)
1. **Pay at Pickup (No Online Payment Gateways):** Strictly per SRS Section 1.5, pre-orders reserve inventory online, and payment is settled in person with the grower at the stall.
2. **In-Person Pickup Only (No Couriers):** Customers select a pickup date and time-slot from the farmer's operating windows.
3. **Map Provider:** OpenStreetMap (via Leaflet.js) is utilized to ensure 100% reliable, zero-cost geolocation markers without API billing restrictions.
4. **Approval Gate:** Newly registered farmers cannot publish weekly inventory until an administrator grants approval from the Admin Dashboard.

---

## 5. Ethical AI Usage & Tool Acknowledgement

In strict alignment with the competition/academic guidelines on AI usage:
> *"AI should be used as a supporting aid rather than a substitute for your own design, development, and problem-solving skills. Do NOT rely on completely ready-made Website templates... Acknowledge all the AI tool(s) used in your project documentation."*

### 5.1 AI Tools Utilized:
- **GitHub Copilot / Antigravity AI Code Assistant:** Used as an intelligent pair-programmer for syntax completion, boilerplate reduction, generating initial PHPUnit test fixtures, and refactoring repetitive HTML markup.
- **Figma / Canva AI:** Used for exploratory layout brainstorming and wireframe spacing inspiration before writing custom, handcrafted CSS tokens.
- **Unsplash Royalty-Free Visuals:** Sourced food photography curated manually to ensure authentic seasonal produce presentation.

### 5.2 Independent Human Engineering & Architecture:
- **Zero Ready-Made Themes/Templates:** MarketLink's UI is written from scratch using a bespoke CSS token design system, responsive Bootstrap 5 foundations, and custom iOS Liquid Glass dropdown micro-interactions.
- **Custom Business Logic & Domain Integrity:** The entire pre-order lifecycle (order window cutoff validation, atomic stock decrementing, and automated inventory restoration upon order decline or customer cancellation) was architected and implemented specifically for this application's domain requirements.
- **Defense Readiness:** Every route, middleware, Eloquent model relationship, migration schema, and controller method has been engineered with clean, idiomatic Laravel standards, enabling complete transparency and justification during evaluation.
