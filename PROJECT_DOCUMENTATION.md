# MarketLink — Comprehensive Technical Project Report
**Theme:** eGreen Basket | **Category:** End-to-End Web Solutions  
**SRS Reference:** Software Requirements Specification Version 1.0  
**Notice:** *In strict accordance with SRS Section 1.9, this documentation does not contain any raw source code.*

---

## 1. Problem Definition & Objectives

### 1.1 Problem Statement
Local farmers markets serve as vital community hubs for fresh, seasonal, and nutrient-dense farm products. However, traditional market dynamics suffer from critical communication friction:
- **Customer Uncertainty:** Shoppers rarely know which growers will be present on a given market morning, what items remain in stock, or what pricing will apply. Many arrive after traveling long distances only to find high-demand products sold out.
- **Grower Inefficiency:** Farmers have lacked a centralized digital channel to advertise their weekly harvest, anticipate customer demand prior to harvest mornings, or take advance reservations.
- **Wasted Trips & Carbon Footprint:** Inability to locate stall pickup points or verify operating hours leads to wasted journeys and food wastage.

### 1.2 Proposed Solution & Objectives
**MarketLink** (*"Farm Fresh Just a Click Away"*) solves these pain points by centralizing weekly inventory, stall geolocation, and pre-order reservations into an integrated, multi-tier web application:
1. **Real-time Weekly Stock Visibility:** Farmers list available quantities and recurring weekly templates.
2. **Interactive Geolocation:** Interactive OpenStreetMap (Leaflet) markers display market locations, farmer stall pins, and pickup routing.
3. **Structured Pre-Order Reservations:** Customers reserve farm products with designated pickup dates and time windows, paying directly at the stall upon collection.
4. **Transparent Community Feedback:** Ratings and written reviews left exclusively by customers who have completed in-person collection.

---

## 2. Design Specifications

### 2.1 Visual Design Tokens & Aesthetic
- **Design Philosophy:** Grounded, organic, and clean editorial styling aligned with the *eGreen Basket* agricultural theme.
- **Color Palette:**
  - **Forest Green (`#15803d` / `#166534`):** Primary branding, buttons, active states, and growth indicators.
  - **Soft Sage (`#f0fdf4` / `#dcfce7`):** Background highlights, badge surfaces, and card headers.
  - **Harvest Gold (`#f59e0b`):** Star ratings, pickup notifications, and market timing badges.
  - **Slate Neutral (`#1e293b` / `#64748b`):** High-contrast typography and subtle structural dividers.
- **Typography:**
  - **Headings:** *Playfair Display* (Editorial serif reflecting artisan tradition and artisanal agriculture).
  - **Body / Interface:** *Plus Jakarta Sans* (Clean, highly legible geometric sans-serif).

### 2.2 Non-Functional Requirements Compliance Matrix (SRS Section 1.7)
| Quality Attribute | Specification Expectation | Concrete MarketLink Engineering Implementation |
|---|---|---|
| **Safe to use** | No malicious or unexpected downloads. | Zero unprompted file triggers; order receipts and admin reports stream sanitized, strictly MIME-typed files (`application/pdf`, `text/csv`). CSRF tokens and Blade HTML-escaping prevent malicious injection. |
| **Accessibility** | Legible fonts, clear UI, and accessible navigation. | Strict WCAG 2.1 AA compliant contrast (`#166534` on `#f0fdf4`), *Playfair Display* & *Plus Jakarta Sans* typographic hierarchy, keyboard-accessible skip-to-content links (`#main-content`), explicit `aria-label` tags, and `prefers-reduced-motion` CSS provisions. |
| **User-friendliness**| Clear menus, easy navigation, intuitive flows. | Text-only persistent navbar, single-click role dashboards, empty states with contextual calls-to-action, universal button hover color transitions, and isolated iOS Liquid Glass dropdown micro-interactions. |
| **Operability** | Reliable, resilient, and graceful operation. | Branded custom error handling (`404.blade.php`, `403.blade.php`, `500.blade.php`) ensuring users are never stranded. Database transactions protect atomic pre-order inventory adjustments. |
| **Performance** | High throughput, minimal load time, smooth redirection. | Lightweight standalone assets (zero heavy node bundling overhead at runtime); database queries utilize Eloquent eager loading (`with()`) to eliminate N+1 latency; catalog displays are paginated (`paginate(12)`). |
| **Scalability** | Capable of handling peak market days with surging users. | Stateless HTTP architecture, 3NF normalized schema with foreign key indexing, and separated customer/farmer/admin query boundaries. |
| **Security** | Adequate authentication and authorization gates. | Role-Based Access Control (`role:admin`, `role:farmer`, `role:customer`), bcrypt password hashing, account deactivation enforcement, unapproved vendor isolation, and order cutoff-window locks. |
| **Availability** | Available 24/7 with zero downtime for pre-orders. | Dual database driver support (instant self-contained SQLite or enterprise MySQL/MariaDB) guaranteeing immediate portability and 24/7 continuous uptime. |
| **Compatibility** | Latest browsers and mobile device responsiveness. | Fully responsive Bootstrap 5 flexbox/grid layout tested across modern Chromium, Gecko, and WebKit rendering engines on mobile, tablet, and widescreen desktop displays. |

---

## 3. System Architecture

MarketLink implements a classic **Multi-Tier Web Architecture** (SRS Section 1.4 & Section 1.6):

```
┌─────────────────────────────────────────────────────────────┐
│                     Presentation Layer                      │
│   Web Browser (Chrome, Firefox, Safari, Edge, Mobile)       │
│   HTML5, CSS3, Bootstrap 5, Leaflet.js, Responsive UI       │
└──────────────────────────────┬──────────────────────────────┘
                               │ HTTP / HTTPS Requests
                               ▼
┌─────────────────────────────────────────────────────────────┐
│                     Application Layer                       │
│   PHP 8.3 / Laravel Framework Engine                        │
│   - Routing & Role-Based Middleware (Admin/Farmer/Customer) │
│   - Session Cart & Order Cutoff State Machine Engine        │
│   - AI Assistant Natural Language FAQ Processor             │
└──────────────────────────────┬──────────────────────────────┘
                               │ PDO / SQL Operations
                               ▼
┌─────────────────────────────────────────────────────────────┐
│                       Database Layer                        │
│   Relational Store (MySQL 5.7+ / SQLite)                    │
│   Normalized 3NF Relational Schema (Users, Markets, etc.)   │
└─────────────────────────────────────────────────────────────┘
```

---

## 4. System Diagrams

### 4.1 System Activity Flowchart
```
[User Arrives at MarketLink]
           │
           ▼
   [Browse Markets & Map]
           │
           ├─► [Filter by Day / Category]
           │
           ▼
   [Select Farm Products]
           │
           ▼
   [Add to Pre-Order Cart]
           │
           ▼
[Authenticated Customer?] ──No──► [Sign In / Register]
           │                                 │
          Yes ◄──────────────────────────────┘
           │
           ▼
[Select Pickup Date & Time Window]
           │
           ▼
[Confirm Pre-Order (Pay at Stall Pickup)]
           │
           ▼
[Order Status: PLACED]
           │
           ▼
[Farmer Reviews & ACCEPTS]
           │
           ▼
[Farmer Marks READY FOR PICKUP] ──► [Customer Alert Triggered]
           │
           ▼
[Customer Arrives at Stall, Pays in Person & Collects Products]
           │
           ▼
[Order Status: COMPLETED]
           │
           ▼
[Customer Submits Star Rating & Review]
```

---

### 4.2 Data Flow Diagram — Level 0 (Context Diagram)
```
                  ┌──────────────────────┐
                  │       Customer       │
                  └──────────┬───────────┘
                             │
            Product Inquiries│Pre-Orders & Reviews
                             ▼
                   ┌────────────────────┐
                   │     MarketLink     │
                   │    Web Solution    │
                   └─────────┬──────────┘
                             ▲
            Stall Stock &    │Order Approvals &
            Pickup Windows   │Review Responses
                             │
                  ┌──────────┴───────────┐
                  │    Farmer / Vendor   │
                  └──────────────────────┘
```

---

### 4.3 Data Flow Diagram — Level 1 (Subsystem Deconstruction)
```
(Customer) ──► [1.0 Market & Map Discovery] ──► [D1: Markets / Stalls Data]
      │
      ├──────► [2.0 Product Search & Filters] ──► [D2: Product Inventory]
      │
      ├──────► [3.0 Pre-Order Reservation] ────► [D3: Orders & Order Items]
      │                                                ▲
(Farmer) ────► [4.0 Inventory & Weekly Template] ──────┤
      │                                                │
      ├──────► [5.0 Pre-Order Processing Queue] ───────┘
      │
(Admin) ─────► [6.0 Approval Gate & Moderation] ──► [D4: Users & Approvals]
```

---

### 4.4 Entity-Relationship Diagram (ERD)

```
+------------------+         1:N         +------------------+
|     MARKETS      |--------------------<|     FARMERS      |
+------------------+                     +------------------+
| PK market_id     |                     | PK farmer_id     |
|    name          |                     | FK user_id       |
|    address       |                     | FK market_id     |
|    operating_days|                     |    stall_name    |
|    timings       |                     |    pickup_windows|
|    latitude      |                     |    cutoff_hours  |
|    longitude     |                     +--------+---------+
+--------+---------+                              |
         |                                        | 1:N
         | 1:N                                    V
         |                               +------------------+
         |                               |     PRODUCTS     |
         |                               +------------------+
         |                               | PK product_id    |
         |                               | FK farmer_id     |
         |                               | FK category_id   |
         |                               |    name          |
         |                               |    price         |
         |                               |    unit          |
         |                               |    stock_quantity|
         |                               +--------+---------+
         |                                        |
         |          1:N                  1:N      |
         +--------------------+     +-------------+
                              |     |
                              V     V
                       +------------------+
                       |   ORDER_ITEMS    |
                       +------------------+
                       | PK order_item_id |
                       | FK order_id      |
                       | FK product_id    |
                       |    quantity      |
                       |    subtotal      |
                       +--------+---------+
                                |
                                | N:1
                                V
+------------------+   1:N     +------------------+
|      USERS       |----------<|      ORDERS      |
+------------------+           +------------------+
| PK user_id       |           | PK order_id      |
|    username      |           | FK customer_id   |
|    email         |           | FK farmer_id     |
|    role          |           | FK market_id     |
|    is_approved   |           |    order_status  |
|    is_active     |           |    pickup_date   |
+--------+---------+           |    pickup_slot   |
         |                     |    total_amount  |
         | 1:N                 |    payment_method|
         V                     +--------+---------+
+------------------+                    |
|     REVIEWS      |<-------------------+ (1:1 upon completion)
+------------------+
| PK review_id     |
| FK order_id      |
| FK customer_id   |
| FK farmer_id     |
|    rating (1-5)  |
|    comment       |
|    farmer_reply  |
+------------------+
```

---

## 5. Comprehensive Database Data Dictionary

### Table 1: `users`
| Field | Type | Constraint | Description |
|---|---|---|---|
| `id` | BIGINT UNSIGNED | PK, Auto Increment | Unique user identifier |
| `username` | VARCHAR(50) | UNIQUE, Nullable | Unique account handle |
| `name` | VARCHAR(100) | NOT NULL | User's full legal or business name |
| `email` | VARCHAR(100) | UNIQUE, NOT NULL | Primary email address |
| `contact_number`| VARCHAR(20) | Nullable | Contact telephone number |
| `address` | TEXT | Nullable | Primary physical address |
| `role` | ENUM | NOT NULL | Role designation: `customer`, `farmer`, `admin` |
| `is_active` | TINYINT(1) | Default 1 | Status toggle for admin customer moderation |
| `is_approved` | TINYINT(1) | Default 1 (Farmers: 0) | Admin approval gate for vendor listing |
| `password` | VARCHAR(255) | NOT NULL | Bcrypt hashed credentials |
| `created_at` | TIMESTAMP | Nullable | Record creation datetime |

### Table 2: `markets`
| Field | Type | Constraint | Description |
|---|---|---|---|
| `id` | BIGINT UNSIGNED | PK, Auto Increment | Unique market identifier |
| `name` | VARCHAR(100) | NOT NULL | Market plaza name |
| `address` | TEXT | NOT NULL | Street address |
| `city` | VARCHAR(50) | NOT NULL | City jurisdiction |
| `operating_days`| VARCHAR(100)| NOT NULL | Days of operation (e.g., Saturday, Sunday) |
| `timings` | VARCHAR(50) | NOT NULL | Daily operating hours |
| `latitude` | DECIMAL(10,8)| NOT NULL | Geographic latitude for OpenStreetMap |
| `longitude`| DECIMAL(11,8)| NOT NULL | Geographic longitude for OpenStreetMap |
| `map_provider` | VARCHAR(30) | Default 'OpenStreetMap' | Geolocation service provider |

### Table 3: `farmers`
| Field | Type | Constraint | Description |
|---|---|---|---|
| `id` | BIGINT UNSIGNED | PK, Auto Increment | Unique farmer profile identifier |
| `user_id` | BIGINT UNSIGNED | FK (`users.id`), CASCADE | Owning user account |
| `market_id` | BIGINT UNSIGNED | FK (`markets.id`), Nullable| Primary assigned market |
| `stall_name` | VARCHAR(100) | NOT NULL | Commercial stall/farm display name |
| `contact_person`| VARCHAR(100)| NOT NULL | Stall manager contact |
| `contact_number`| VARCHAR(20) | NOT NULL | Vendor telephone |
| `latitude` | DECIMAL(10,8)| Nullable | Exact stall marker coordinate |
| `longitude`| DECIMAL(11,8)| Nullable | Exact stall marker coordinate |
| `operating_days`| VARCHAR(100)| Nullable | Stall operating days |
| `pickup_time_windows`| TEXT | Nullable | Available pickup time slots |
| `cutoff_hours` | INT | Default 2 | Hours before pickup when orders lock |
| `bio` | TEXT | Nullable | Stall biography and farming philosophy |

### Table 4: `products`
| Field | Type | Constraint | Description |
|---|---|---|---|
| `id` | BIGINT UNSIGNED | PK, Auto Increment | Unique product identifier |
| `farmer_id` | BIGINT UNSIGNED | FK (`farmers.id`), CASCADE | Producing farmer stall |
| `category_id` | BIGINT UNSIGNED | FK (`categories.id`), CASCADE | Master category classification |
| `name` | VARCHAR(100) | NOT NULL | Product title |
| `description` | TEXT | Nullable | Detailed harvest description |
| `price` | DECIMAL(10,2)| NOT NULL | Unit price |
| `unit` | VARCHAR(20) | NOT NULL | Measurement unit (kg, bunch, box, dozen) |
| `stock_quantity`| INT | NOT NULL, Default 0 | Real-time available stock count |
| `weekly_recurring_stock`| INT | NOT NULL, Default 0 | Baseline weekly replenishment template |
| `is_sold_out` | TINYINT(1) | Default 0 | Immediate out-of-stock toggle |
| `is_available`| TINYINT(1) | Default 1 | Vendor publication visibility toggle |

### Table 5: `orders`
| Field | Type | Constraint | Description |
|---|---|---|---|
| `id` | BIGINT UNSIGNED | PK, Auto Increment | Unique pre-order identifier |
| `order_number`| VARCHAR(50) | UNIQUE, NOT NULL | Human-readable tracking number |
| `customer_id` | BIGINT UNSIGNED | FK (`users.id`), CASCADE | Reserving customer |
| `farmer_id` | BIGINT UNSIGNED | FK (`farmers.id`), CASCADE | Target vendor stall |
| `market_id` | BIGINT UNSIGNED | FK (`markets.id`), Nullable| Market collection venue |
| `order_status`| ENUM | NOT NULL | `placed`, `accepted`, `ready_for_pickup`, `completed`, `cancelled`, `declined` |
| `pickup_date` | DATE | NOT NULL | Scheduled collection date |
| `pickup_time_slot`| VARCHAR(50)| NOT NULL | Booked time window |
| `total_amount`| DECIMAL(10,2)| NOT NULL | Sum due in person at stall |
| `payment_method`| VARCHAR(50)| Default 'pay_at_pickup'| Strictly in-person settlement (SRS §1.5) |
| `cutoff_time` | TIMESTAMP | Nullable | Calculated modification deadline |

### Table 6: `order_items`
| Field | Type | Constraint | Description |
|---|---|---|---|
| `id` | BIGINT UNSIGNED | PK, Auto Increment | Unique line item identifier |
| `order_id` | BIGINT UNSIGNED | FK (`orders.id`), CASCADE | Parent order reservation |
| `product_id` | BIGINT UNSIGNED | FK (`products.id`), CASCADE | Reserved catalog product |
| `quantity` | INT | NOT NULL | Item quantity ordered |
| `unit_price` | DECIMAL(10,2)| NOT NULL | Unit price at time of reservation |
| `subtotal` | DECIMAL(10,2)| NOT NULL | Calculated line total |

### Table 7: `reviews`
| Field | Type | Constraint | Description |
|---|---|---|---|
| `id` | BIGINT UNSIGNED | PK, Auto Increment | Unique review identifier |
| `order_id` | BIGINT UNSIGNED | FK (`orders.id`), CASCADE | Verified completed order |
| `customer_id` | BIGINT UNSIGNED | FK (`users.id`), CASCADE | Reviewing customer |
| `farmer_id` | BIGINT UNSIGNED | FK (`farmers.id`), CASCADE | Stall reviewed |
| `product_id` | BIGINT UNSIGNED | FK (`products.id`), Nullable| Specific reviewed product |
| `rating` | TINYINT UNSIGNED| NOT NULL | Star rating (1 to 5) |
| `comment` | TEXT | NOT NULL | Customer feedback statement |
| `farmer_response`| TEXT | Nullable | Vendor public response |
| `created_at` | TIMESTAMP | Nullable | Review submission timestamp (`review_date`) |

### Table 8: `reports` (Analytics & Export Summary)
| Field | Type | Constraint | Description |
|---|---|---|---|
| `id` | BIGINT UNSIGNED | PK, Auto Increment | Unique report tracking identifier |
| `generated_by` | BIGINT UNSIGNED | FK (`users.id`), CASCADE | Administrator who generated the report |
| `report_type` | VARCHAR(50) | NOT NULL | Category (e.g., `orders_summary`, `revenue_by_market`, `active_farmers_csv`) |
| `generated_at` | TIMESTAMP | NOT NULL | Datetime when the report was compiled/exported |

---

## 6. Test Data Inventory (SRS Section 1.9)

Pre-loaded test data covers all user scenarios across the application:
1. **Three Markets Geocoded:**
   - Downtown Farmers Plaza (Saturdays & Sundays, 08:00 AM - 02:00 PM)
   - Riverside Green & Artisan Market (Wednesdays & Saturdays, 09:00 AM - 03:00 PM)
   - Oak Valley Community Harvest Fair (Sundays, 07:30 AM - 01:30 PM)
2. **Three Diverse Farmer Vendor Stalls:**
   - *Green Valley Organic Farm:* Heirloom tomatoes, organic kale bundles, Japanese sweet potatoes, pasture-raised brown eggs.
   - *Sunshine Orchards & Apiary:* Honeycrisp apples, wildflower honeycomb, rustic sourdough batards.
   - *New Harvest Urban Greens:* Microgreens and culinary herbs (Pending approval scenario).
3. **Pre-Seeded Orders:**
   - Completed order with verified star ratings and vendor reply.
   - Active pre-order marked `ready_for_pickup` with an unread in-app notification.

---

## 7. Video Demonstration Script (.mp4 Submission Guide)

*Mandatory deliverable per SRS Section 1.9: A 5–7 minute walkthrough demonstrating all functional requirements.*

| Scene | Duration | Action & Screen Focus | Voiceover / Talking Points |
|---|---|---|---|
| **1. Introduction** | 0:00 - 0:45 | Home page, eGreen Basket theme, announcement ribbon. | "Welcome to MarketLink, our end-to-end web platform connecting local growers with conscious consumers..." |
| **2. Geolocation Discovery** | 0:45 - 1:45 | Navigate to Markets & Map, filter by Saturday, click stall marker, view OpenStreetMap directions. | "Here we explore local markets on an embedded OpenStreetMap with live stall pins and pickup routes..." |
| **3. Catalog & Filters** | 1:45 - 2:30 | Product catalog, apply price and category filters, search for 'Tomatoes'. | "Customers can browse fresh stock with multi-parameter filtering..." |
| **4. AI Assistant FAQ** | 2:30 - 3:15 | Click floating AI widget, ask market hours and product availability. | "Our integrated AI chatbot provides real-time answers on market schedules and pickup policies..." |
| **5. Pre-Order & Checkout** | 3:15 - 4:15 | Add products to cart, select pickup date and time window, place pre-order. | "Pre-orders reserve live stock. Notice: zero payment gateways are used; payment is settled in person..." |
| **6. Customer Dashboard** | 4:15 - 5:00 | View order tracking pipeline, cutoff cancellation rules, 1-click re-order. | "Customers track their order progression from placed to ready for pickup..." |
| **7. Farmer & Admin Portals** | 5:00 - 6:00 | Sign into farmer dashboard (sales metrics, incoming orders), sign into admin dashboard (farmer approval gate). | "Farmers manage orders and weekly templates, while administrators oversee vendor approvals..." |
| **8. Conclusion** | 6:00 - 6:30 | Wrap-up, showing About Us and Contact Us with team map. | "MarketLink: Farm Fresh Just a Click Away. Thank you." |

---

## 8. Ethical AI Usage & Tools Acknowledgement

In compliance with academic, competition, and submission directives regarding responsible AI usage:

### 8.1 Principles Followed:
- **No Ready-Made Templates:** The web application was built from the ground up without using off-the-shelf website builders or downloaded theme packs.
- **Architectural Ownership:** The database design, data relationships (Users, Farmers, Markets, Products, Orders, OrderItems, Reviews, Favorites), authorization gates, and transaction lifecycles were designed and understood completely by the engineering team.
- **Human-Driven Problem Solving:** The core scheduling constraints (farmer pickup time windows, cut-off hour calculations, atomic inventory reservation, and restitution) were hand-coded to specifically fulfill local farmers-market dynamics.

### 8.2 AI Tools Utilized & Roles:
| AI Tool / Resource | Category | Specific Role in Development |
|---|---|---|
| **GitHub Copilot / Antigravity AI** | Code Assistance | Syntax autocomplete, boilerplate reduction, generating initial PHPUnit test fixtures, and assisting in regex / data transformation routines. |
| **Figma AI / Canva** | UI / Wireframing | Brainstorming visual layout balance, color-harmony exploration, and card spacing hierarchy prior to handcrafted CSS implementation. |
| **Unsplash Curated Library** | Visual Assets | Authenticated, high-resolution photography for farm products and seasonal harvest. |

### 8.3 Judge & Evaluator Defense Readiness:
Every design decision, controller logic path, and styling abstraction is fully documented and understood by the team, including:
1. **Zero-Gateway Model (`pay_at_pickup`):** Rationale for eliminating payment processor dependencies to reflect authentic farmers market cash/card stall transactions and avoid payment platform fees.
2. **OpenStreetMap Integration:** Technical decision to use Leaflet.js with OSM tiles for resilient, cost-free, API-key-independent geolocation.
3. **Cut-off Window Logic:** How pre-order modifications and cancellations are blocked based on the farmer's configured cutoff hours before market day.
4. **State Machine Verification:** Full inventory safety guarantees across `placed`, `accepted`, `ready_for_pickup`, `completed`, `cancelled`, and `declined` states.

---

## 9. Project Installation Instructions (MANDATORY per SRS Section 1.9)

### 9.1 Environment Prerequisites:
- **PHP Engine:** PHP 8.2 or 8.3 (with `pdo_sqlite`, `pdo_mysql`, `curl`, `mbstring`, `fileinfo`, `gd`, `openssl` extensions enabled).
- **Dependency Manager:** Composer 2.x.
- **Web Server Options:** Built-in PHP development server (`php artisan serve`), XAMPP (Apache + MySQL), or Nginx with PHP-FPM.

### 9.2 Step-by-Step Setup Procedure:
1. **Extract and Navigate:**
   Extract the submitted zip file and open your terminal in the application root directory:
   ```
   cd MarketLink
   ```
2. **Install PHP Dependencies:**
   ```
   composer install
   ```
3. **Initialize Environment Variables:**
   Copy the example environment configuration and generate the unique application encryption key:
   ```
   copy .env.example .env
   php artisan key:generate
   ```
4. **Database Initialization (Two Options):**
   - **Option A (Instant Zero-Config SQLite — Recommended):**
     ```
     php artisan migrate:fresh --seed
     ```
     *Automatically provisions all tables and populates pre-seeded evaluation records immediately without configuring MySQL services.*
   - **Option B (MySQL / XAMPP phpMyAdmin):**
     1. Start Apache and MySQL services in the XAMPP Control Panel.
     2. Navigate to `http://localhost/phpmyadmin` in your web browser.
     3. Create a new database named `marketlink`.
     4. Import the provided `marketlink.sql` file located in the project root directory.
     5. Update `.env` database parameters: `DB_CONNECTION=mysql`, `DB_DATABASE=marketlink`, `DB_USERNAME=root`, `DB_PASSWORD=`.
5. **Launch Local Application Server:**
   ```
   php artisan serve
   ```
   Access the running application in your web browser at: `http://localhost:8000`

---

## 10. User Credentials for All Types of Users with Passwords (MANDATORY per SRS Section 1.9)

Pre-seeded evaluation accounts provide immediate access across all defined roles:

| Role Type | Username | Email Address | Password | Role Capabilities & Evaluation Purpose |
|---|---|---|---|---|
| **System Administrator** | `admin` | `admin@marketlink.local` | `Admin@123` | Dedicated backoffice dashboard (`/admin/dashboard`), vendor approval gate, customer moderation/deactivation, market plaza management, product/review moderation, and platform CSV analytics generation. |
| **Approved Farmer (Primary)** | `greenvalley` | `farmer@marketlink.local` | `Farmer@123` | Active vendor profile (*Green Valley Organic Farm*), catalog inventory management, weekly recurring stock templates, order pipeline (`accepted`, `ready_for_pickup`, `completed`, `declined`), and review replies. |
| **Approved Farmer (Secondary)** | `sunshineorchard` | `orchard@marketlink.local` | `Farmer@123` | Multi-vendor scenario (*Sunshine Orchards & Apiary* at Riverside Green Market) demonstrating cross-vendor pre-order separation and stall geolocation. |
| **Pending Farmer (Approval Gate)** | `newharvest` | `newharvest@marketlink.local` | `Farmer@123` | Demonstrates the mandatory vendor onboarding gate: newly registered stalls are completely quarantined from public storefront search until an Administrator inspects and approves their registration. |
| **Customer (Primary)** | `sarah_shopper` | `customer@marketlink.local` | `Customer@123` | Active shopper account loaded with active orders, pickup schedule modification, cutoff cancellation, 1-click reorder, saved favorites (markets, farmers, products), and receipt PDF downloads. |
| **Customer (Secondary)** | `david_miller` | `david@marketlink.local` | `Customer@123` | Multi-customer concurrent pre-order testing and verified product review submission. |

