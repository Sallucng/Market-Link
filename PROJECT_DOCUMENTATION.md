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

### 2.2 Usability & Accessibility (SRS Section 1.7)
- **High Contrast Ratios:** All text elements meet WCAG AA contrast standards.
- **Touch-Friendly Hit Targets:** Minimum 44x44px target sizes for mobile pickup slot selection and navigation.
- **Responsive Layout:** Fluid grid adjusting seamlessly across 320px mobile screens, tablets, and widescreen desktop monitors.

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
