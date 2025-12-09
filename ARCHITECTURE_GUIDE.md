# Al-Tahir Graphics - Architecture Guide

## 🏗️ System Architecture Overview

### High-Level Architecture

```
┌─────────────────────────────────────────────────────────────────┐
│                         USER LAYER                               │
├─────────────────────────────────────────────────────────────────┤
│  Customers  │  Designers  │  Support Staff  │  Administrators  │
└──────┬──────┴──────┬──────┴────────┬────────┴────────┬──────────┘
       │             │               │                 │
       └─────────────┴───────────────┴─────────────────┘
                            │
                            ▼
┌─────────────────────────────────────────────────────────────────┐
│                    PRESENTATION LAYER                            │
├─────────────────────────────────────────────────────────────────┤
│  Blade Templates + Tailwind CSS + Alpine.js + AOS               │
│  ┌──────────────┐  ┌──────────────┐  ┌──────────────┐         │
│  │ Public Pages │  │ Admin Panel  │  │ Chat Widget  │         │
│  └──────────────┘  └──────────────┘  └──────────────┘         │
└─────────────────────────────────────────────────────────────────┘
                            │
                            ▼
┌─────────────────────────────────────────────────────────────────┐
│                      ROUTING LAYER                               │
├─────────────────────────────────────────────────────────────────┤
│  web.php  │  admin.php  │  auth.php  │  chat.php              │
└─────────────────────────────────────────────────────────────────┘
                            │
                            ▼
┌─────────────────────────────────────────────────────────────────┐
│                     MIDDLEWARE LAYER                             │
├─────────────────────────────────────────────────────────────────┤
│  Auth  │  Admin  │  Designer  │  CSRF  │  Rate Limit           │
└─────────────────────────────────────────────────────────────────┘
                            │
                            ▼
┌─────────────────────────────────────────────────────────────────┐
│                    CONTROLLER LAYER                              │
├─────────────────────────────────────────────────────────────────┤
│  ┌────────────┐  ┌────────────┐  ┌────────────┐               │
│  │  Public    │  │   Admin    │  │    Chat    │               │
│  │ Controllers│  │ Controllers│  │ Controllers│               │
│  └────────────┘  └────────────┘  └────────────┘               │
└─────────────────────────────────────────────────────────────────┘
                            │
                            ▼
┌─────────────────────────────────────────────────────────────────┐
│                    BUSINESS LOGIC LAYER                          │
├─────────────────────────────────────────────────────────────────┤
│  Models  │  Services  │  Helpers  │  Jobs  │  Events           │
└─────────────────────────────────────────────────────────────────┘
                            │
                            ▼
┌─────────────────────────────────────────────────────────────────┐
│                     DATA ACCESS LAYER                            │
├─────────────────────────────────────────────────────────────────┤
│  Eloquent ORM  │  Query Builder  │  Migrations                 │
└─────────────────────────────────────────────────────────────────┘
                            │
                            ▼
┌─────────────────────────────────────────────────────────────────┐
│                      DATABASE LAYER                              │
├─────────────────────────────────────────────────────────────────┤
│  SQLite Database (database.sqlite)                              │
└─────────────────────────────────────────────────────────────────┘
```

---

## 🔄 Request Flow Diagram

### Public Customer Request Flow

```
User Browser
    │
    │ HTTP Request
    ▼
┌─────────────────┐
│   Web Server    │ (Apache/Nginx)
│  public/index.php│
└────────┬────────┘
         │
         │ Bootstrap Laravel
         ▼
┌─────────────────┐
│  Route Matching │ (routes/web.php)
└────────┬────────┘
         │
         │ Match route: /services/{slug}
         ▼
┌─────────────────┐
│   Middleware    │ (Check auth, CSRF, etc.)
└────────┬────────┘
         │
         │ Pass through
         ▼
┌─────────────────┐
│   Controller    │ ServiceController@show
└────────┬────────┘
         │
         │ Call model
         ▼
┌─────────────────┐
│     Model       │ Service::with('samples')->findBySlug()
└────────┬────────┘
         │
         │ Query database
         ▼
┌─────────────────┐
│    Database     │ SELECT * FROM services...
└────────┬────────┘
         │
         │ Return data
         ▼
┌─────────────────┐
│     View        │ resources/views/services/show.blade.php
└────────┬────────┘
         │
         │ Render HTML
         ▼
┌─────────────────┐
│  HTTP Response  │
└────────┬────────┘
         │
         ▼
    User Browser
```

### Admin Request Flow (with Authorization)

```
Admin Browser
    │
    │ POST /admin/orders/{id}/assign-designer
    ▼
┌─────────────────┐
│   Web Server    │
└────────┬────────┘
         │
         ▼
┌─────────────────┐
│  Route Matching │ (routes/admin.php)
└────────┬────────┘
         │
         ▼
┌─────────────────┐
│  Auth Middleware│ Check if logged in
└────────┬────────┘
         │ ✓ Authenticated
         ▼
┌─────────────────┐
│ Admin Middleware│ Check if is_admin = true
└────────┬────────┘
         │ ✓ Is Admin
         ▼
┌─────────────────┐
│   Controller    │ Admin\OrderController@assignDesigner
└────────┬────────┘
         │
         │ 1. Validate request
         ▼
┌─────────────────┐
│ Form Validation │
└────────┬────────┘
         │ ✓ Valid
         │ 2. Update order
         ▼
┌─────────────────┐
│  Order Model    │ $order->update(['assigned_designer_id' => ...])
└────────┬────────┘
         │
         │ 3. Create audit log
         ▼
┌─────────────────┐
│  AuditLog Model │ AuditLog::create([...])
└────────┬────────┘
         │
         │ 4. Send notification (optional)
         ▼
┌─────────────────┐
│  Mail System    │ Mail::to($designer)->send(...)
└────────┬────────┘
         │
         │ 5. Return response
         ▼
┌─────────────────┐
│  JSON Response  │ { "success": true, "message": "..." }
└────────┬────────┘
         │
         ▼
    Admin Browser (Update UI)
```

---

## 🛒 Shopping Cart Architecture

### Cart System Flow

```
┌──────────────────────────────────────────────────────────────┐
│                    CART ARCHITECTURE                          │
└──────────────────────────────────────────────────────────────┘

        Guest User                    Authenticated User
            │                                  │
            │                                  │
            ▼                                  ▼
    ┌──────────────┐                  ┌──────────────┐
    │ localStorage │                  │ localStorage │
    │   (Backup)   │                  │  + Database  │
    └──────┬───────┘                  └──────┬───────┘
           │                                  │
           │                                  │
           │    On Login                      │
           └────────────┬─────────────────────┘
                        │
                        ▼
              ┌──────────────────┐
              │  Cart Sync API   │
              │  POST /cart/sync │
              └────────┬─────────┘
                       │
                       │ Merge carts
                       ▼
              ┌──────────────────┐
              │   Database Cart  │
              │   (cart_items)   │
              └──────────────────┘

Cart Operations:
┌────────────┐   ┌────────────┐   ┌────────────┐   ┌────────────┐
│    Add     │   │   Update   │   │   Remove   │   │   Clear    │
│ Item to    │→  │ Quantity   │→  │    Item    │→  │    Cart    │
│   Cart     │   │            │   │            │   │            │
└────────────┘   └────────────┘   └────────────┘   └────────────┘
      │                │                │                │
      └────────────────┴────────────────┴────────────────┘
                              │
                              ▼
                    ┌──────────────────┐
                    │ Update localStorage│
                    │ Update Database   │
                    │ Recalculate Total │
                    └──────────────────┘
```

---

## 📦 Order Processing Architecture

### Order Lifecycle

```
┌─────────────────────────────────────────────────────────────────┐
│                    ORDER PROCESSING FLOW                         │
└─────────────────────────────────────────────────────────────────┘

    Customer                    System                     Admin
       │                          │                          │
       │  1. Add to Cart         │                          │
       │─────────────────────────→                          │
       │                          │                          │
       │  2. Proceed to Checkout │                          │
       │─────────────────────────→                          │
       │                          │                          │
       │                    ┌─────────────┐                 │
       │                    │  Validate   │                 │
       │                    │  Cart Items │                 │
       │                    └─────────────┘                 │
       │                          │                          │
       │  3. Enter Details        │                          │
       │─────────────────────────→                          │
       │                          │                          │
       │                    ┌─────────────┐                 │
       │                    │  Generate   │                 │
       │                    │ Order Number│                 │
       │                    │ATG20250109XX│                 │
       │                    └─────────────┘                 │
       │                          │                          │
       │  4. Choose Payment       │                          │
       │─────────────────────────→                          │
       │                          │                          │
       │  If Bank Transfer:       │                          │
       │  5. Upload Screenshot    │                          │
       │─────────────────────────→                          │
       │                          │                          │
       │                    ┌─────────────┐                 │
       │                    │Create Order │                 │
       │                    │ Status:     │                 │
       │                    │  pending    │                 │
       │                    │ Payment:    │                 │
       │                    │pending_verif│                 │
       │                    └─────────────┘                 │
       │                          │                          │
       │                          │  6. Send Notification    │
       │                          │─────────────────────────→│
       │                          │                          │
       │  7. Order Confirmation   │                          │
       │←─────────────────────────                          │
       │    (Email)               │                          │
       │                          │                          │
       │                          │  8. Review Payment       │
       │                          │←─────────────────────────│
       │                          │                          │
       │                    ┌─────────────┐                 │
       │                    │ Verify      │                 │
       │                    │ Payment     │                 │
       │                    │ Status: paid│                 │
       │                    └─────────────┘                 │
       │                          │                          │
       │                          │  9. Assign Designer      │
       │                          │←─────────────────────────│
       │                          │                          │
       │                    ┌─────────────┐                 │
       │                    │ Update      │                 │
       │                    │ Design      │                 │
       │                    │ Status:     │                 │
       │                    │in_progress  │                 │
       │                    └─────────────┘                 │
       │                          │                          │
       │  10. Status Updates      │                          │
       │←─────────────────────────                          │
       │     (Email)              │                          │
       │                          │                          │
       │                          │ 11. Generate Invoice     │
       │                          │←─────────────────────────│
       │                          │                          │
       │                    ┌─────────────┐                 │
       │                    │ Create PDF  │                 │
       │                    │ Invoice     │                 │
       │                    └─────────────┘                 │
       │                          │                          │
       │  12. Invoice Email       │                          │
       │←─────────────────────────                          │
       │                          │                          │
       │                          │ 13. Mark Completed       │
       │                          │←─────────────────────────│
       │                          │                          │
       │  14. Completion Email    │                          │
       │←─────────────────────────                          │
       │                          │                          │
```

---

## 💬 Chat System Architecture

### Real-time Chat via Database Polling

```
┌─────────────────────────────────────────────────────────────────┐
│              CHAT SYSTEM ARCHITECTURE                            │
└─────────────────────────────────────────────────────────────────┘

Customer Side                  Server                    Admin Side
     │                           │                            │
     │ 1. Open Chat Widget      │                            │
     │──────────────────────────→                            │
     │                           │                            │
     │                     ┌──────────┐                      │
     │                     │ Create/  │                      │
     │                     │ Find Chat│                      │
     │                     │ Session  │                      │
     │                     └──────────┘                      │
     │                           │                            │
     │ 2. Start Polling (3s)    │                            │
     │──────────────────────────→                            │
     │                           │                            │
     │                     ┌──────────┐                      │
     │                     │ Query DB │                      │
     │                     │ for new  │                      │
     │                     │ messages │                      │
     │                     └──────────┘                      │
     │                           │                            │
     │ 3. Send Message          │                            │
     │──────────────────────────→                            │
     │                           │                            │
     │                     ┌──────────┐                      │
     │                     │ Insert   │                      │
     │                     │ Message  │                      │
     │                     │ to DB    │                      │
     │                     └──────────┘                      │
     │                           │                            │
     │                           │ 4. Notify Admin (Email)   │
     │                           │───────────────────────────→│
     │                           │                            │
     │                           │ 5. Admin Polls for Chats  │
     │                           │←───────────────────────────│
     │                           │                            │
     │                     ┌──────────┐                      │
     │                     │ Return   │                      │
     │                     │ Unread   │                      │
     │                     │ Messages │                      │
     │                     └──────────┘                      │
     │                           │                            │
     │                           │ 6. Admin Sends Reply      │
     │                           │←───────────────────────────│
     │                           │                            │
     │                     ┌──────────┐                      │
     │                     │ Insert   │                      │
     │                     │ Reply    │                      │
     │                     │ to DB    │                      │
     │                     └──────────┘                      │
     │                           │                            │
     │ 7. Poll Detects New Msg  │                            │
     │←──────────────────────────                            │
     │                           │                            │
     │ 8. Display Message        │                            │
     │                           │                            │
     │ 9. Mark as Read          │                            │
     │──────────────────────────→                            │
     │                           │                            │
     │                     ┌──────────┐                      │
     │                     │ Update   │                      │
     │                     │ is_read  │                      │
     │                     │ field    │                      │
     │                     └──────────┘                      │
     │                           │                            │

Database Tables Used:
┌────────────────────┐
│  unified_chats     │  ← Main chat session
├────────────────────┤
│  - id              │
│  - type            │
│  - status          │
│  - created_by      │
│  - assigned_to     │
│  - last_message_at │
└────────────────────┘
         │
         │ has many
         ▼
┌────────────────────────┐
│ unified_chat_messages  │  ← Individual messages
├────────────────────────┤
│  - id                  │
│  - unified_chat_id     │
│  - sender_id           │
│  - message             │
│  - is_read             │
│  - read_at             │
└────────────────────────┘
         │
         │ has many
         ▼
┌────────────────────┐
│ chat_participants  │  ← Users in chat
├────────────────────┤
│  - id              │
│  - unified_chat_id │
│  - user_id         │
│  - is_active       │
│  - last_read_at    │
└────────────────────┘
```

### Polling Mechanism

```javascript
// Client-side polling (every 3 seconds)
setInterval(() => {
    fetch(`/chat/messages/${chatId}?since=${lastMessageTime}`)
        .then(response => response.json())
        .then(data => {
            if (data.messages.length > 0) {
                displayNewMessages(data.messages);
                lastMessageTime = data.messages[data.messages.length - 1].created_at;
            }
        });
}, 3000);
```

---

## 🔐 Authentication & Authorization Architecture

### Authentication Flow

```
┌─────────────────────────────────────────────────────────────────┐
│              AUTHENTICATION ARCHITECTURE                         │
└─────────────────────────────────────────────────────────────────┘

User
 │
 │ 1. Visit /login
 ▼
┌──────────────┐
│ Login Form   │
└──────┬───────┘
       │
       │ 2. Submit credentials
       ▼
┌──────────────┐
│ Validate     │
│ Credentials  │
└──────┬───────┘
       │
       │ ✓ Valid
       ▼
┌──────────────┐
│ Create       │
│ Session      │
└──────┬───────┘
       │
       │ Store in cookie
       ▼
┌──────────────┐
│ Remember     │
│ Token        │ (if "remember me")
└──────┬───────┘
       │
       │ Redirect
       ▼
┌──────────────┐
│ Dashboard    │
└──────────────┘

Session Structure:
┌────────────────────────┐
│  Session Cookie        │
├────────────────────────┤
│  - User ID             │
│  - Authentication Hash │
│  - CSRF Token          │
│  - Expiration          │
└────────────────────────┘
```

### Authorization Flow

```
┌─────────────────────────────────────────────────────────────────┐
│              AUTHORIZATION ARCHITECTURE                          │
└─────────────────────────────────────────────────────────────────┘

Authenticated User
       │
       │ Request: /admin/users
       ▼
┌──────────────┐
│ Check Session│ auth middleware
└──────┬───────┘
       │ ✓ Logged in
       ▼
┌──────────────┐
│ Check if     │ AdminMiddleware
│ is_admin = 1 │
└──────┬───────┘
       │ ✓ Is admin
       ▼
┌──────────────┐
│ Check        │ Spatie Permission
│ Permissions  │
└──────┬───────┘
       │
       │ Has "manage users" permission?
       │
       ├── ✓ Yes ─────→ Allow Access
       │
       └── ✗ No ──────→ 403 Forbidden

Permission Check Methods:
┌────────────────────────────────┐
│ $user->hasRole('admin')        │ → Check role
│ $user->can('manage users')     │ → Check permission
│ $user->hasPermissionTo('...')  │ → Direct check
│ @can('manage-users')           │ → Blade directive
└────────────────────────────────┘
```

---

## 📊 Database Architecture

### Entity Relationship Diagram (ERD)

```
┌─────────────────────────────────────────────────────────────────┐
│                  DATABASE RELATIONSHIPS                          │
└─────────────────────────────────────────────────────────────────┘

┌──────────┐
│  users   │
└────┬─────┘
     │
     │ has many
     ├─────────────┐
     │             │
     ▼             ▼
┌─────────┐   ┌──────────┐
│ orders  │   │cart_items│
└────┬────┘   └──────────┘
     │
     │ has many
     ▼
┌─────────────┐
│ order_items │
└────┬────────┘
     │
     │ belongs to
     ▼
┌──────────────┐
│   services   │
└────┬─────────┘
     │
     │ has many
     ▼
┌────────────────────┐
│ service_samples    │
└────────────────────┘

┌──────────┐
│  users   │
└────┬─────┘
     │
     │ creates
     ▼
┌──────────────┐
│unified_chats │
└────┬─────────┘
     │
     │ has many
     ├────────────────┬─────────────────┐
     │                │                 │
     ▼                ▼                 ▼
┌─────────────┐  ┌──────────┐  ┌─────────────┐
│  messages   │  │participants│  │ attachments │
└─────────────┘  └──────────┘  └─────────────┘

┌──────────┐
│ orders   │
└────┬─────┘
     │
     │ has many
     ├────────────────┬─────────────────┐
     │                │                 │
     ▼                ▼                 ▼
┌──────────┐  ┌─────────────┐  ┌──────────┐
│ invoices │  │ order_notes │  │ quotes   │
└──────────┘  └─────────────┘  └──────────┘

Permission System (Spatie):
┌──────────┐     ┌──────────────────┐     ┌─────────────┐
│  users   │────→│model_has_roles   │←────│    roles    │
└──────────┘     └──────────────────┘     └──────┬──────┘
                                                   │
                                                   ▼
                                          ┌──────────────────┐
                                          │role_has_permissions│
                                          └──────────┬─────────┘
                                                     │
                                                     ▼
                                             ┌──────────────┐
                                             │ permissions  │
                                             └──────────────┘
```

### Database Indexes

```sql
-- Performance Indexes
CREATE INDEX idx_orders_status ON orders(order_status);
CREATE INDEX idx_orders_payment ON orders(payment_status);
CREATE INDEX idx_orders_user ON orders(user_id);
CREATE INDEX idx_orders_designer ON orders(assigned_designer_id);

CREATE INDEX idx_messages_chat ON unified_chat_messages(unified_chat_id);
CREATE INDEX idx_messages_read ON unified_chat_messages(is_read);
CREATE INDEX idx_messages_time ON unified_chat_messages(created_at);

CREATE INDEX idx_services_active ON services(is_active);
CREATE INDEX idx_services_featured ON services(is_featured);
CREATE INDEX idx_services_slug ON services(slug);
```

---

## 📧 Email Notification Architecture

### Email Flow

```
┌─────────────────────────────────────────────────────────────────┐
│                  EMAIL NOTIFICATION FLOW                         │
└─────────────────────────────────────────────────────────────────┘

Event Trigger              Email Queue              Email Delivery
     │                          │                          │
     │ Order Created            │                          │
     │─────────────────────────→                          │
     │                          │                          │
     │                    ┌──────────┐                    │
     │                    │Queue Job │                    │
     │                    │SendEmail │                    │
     │                    └────┬─────┘                    │
     │                         │                          │
     │                         │ Process                  │
     │                         ▼                          │
     │                    ┌──────────┐                    │
     │                    │ Build    │                    │
     │                    │ Email    │                    │
     │                    │ from     │                    │
     │                    │ Template │                    │
     │                    └────┬─────┘                    │
     │                         │                          │
     │                         │ Send via SMTP            │
     │                         │─────────────────────────→│
     │                         │                          │
     │                         │                    ┌──────────┐
     │                         │                    │ Deliver  │
     │                         │                    │ to       │
     │                         │                    │ Customer │
     │                         │                    └──────────┘
     │                         │                          │
     │                    ┌──────────┐                    │
     │                    │  Log     │                    │
     │                    │ Success/ │                    │
     │                    │  Failure │                    │
     │                    └──────────┘                    │

Email Types:
┌────────────────────────┐
│ OrderConfirmationMail  │ → Customer (Order placed)
├────────────────────────┤
│ OrderNotificationMail  │ → Admin (New order alert)
├────────────────────────┤
│ ChatNotificationMail   │ → User/Admin (New message)
├────────────────────────┤
│ InvoiceMail            │ → Customer (Invoice sent)
├────────────────────────┤
│ PaymentVerifiedMail    │ → Customer (Payment confirmed)
└────────────────────────┘
```

---

## 🚀 Deployment Architecture

### Production Environment

```
┌─────────────────────────────────────────────────────────────────┐
│                   PRODUCTION ARCHITECTURE                        │
└─────────────────────────────────────────────────────────────────┘

                        ┌──────────────┐
                        │   Internet   │
                        └───────┬──────┘
                                │
                                ▼
                        ┌──────────────┐
                        │   Firewall   │
                        └───────┬──────┘
                                │
                                ▼
                        ┌──────────────┐
                        │ Load Balancer│ (Optional)
                        └───────┬──────┘
                                │
                ┌───────────────┴───────────────┐
                │                               │
                ▼                               ▼
        ┌──────────────┐              ┌──────────────┐
        │ Web Server 1 │              │ Web Server 2 │
        │ (Apache/Nginx)│              │ (Apache/Nginx)│
        └───────┬──────┘              └───────┬──────┘
                │                               │
                └───────────────┬───────────────┘
                                │
                                ▼
                        ┌──────────────┐
                        │Laravel App   │
                        │              │
                        │ ┌──────────┐ │
                        │ │ Cache    │ │
                        │ └──────────┘ │
                        │              │
                        │ ┌──────────┐ │
                        │ │ Queue    │ │
                        │ │ Worker   │ │
                        │ └──────────┘ │
                        └───────┬──────┘
                                │
                ┌───────────────┼───────────────┐
                ▼               ▼               ▼
        ┌──────────────┐ ┌──────────┐ ┌──────────────┐
        │   Database   │ │ Storage  │ │ Mail Server  │
        │   (SQLite/   │ │ (Files)  │ │   (SMTP)     │
        │    MySQL)    │ │          │ │              │
        └──────────────┘ └──────────┘ └──────────────┘
```

---

## 🔄 Data Flow Diagrams

### Service Display Flow

```
User → Browse Services → ServiceController
                              │
                              ├→ Get featured services
                              │  Service::featured()->active()->get()
                              │
                              ├→ Get by category
                              │  Service::where('category', $cat)->get()
                              │
                              └→ Get single service with samples
                                 Service::with('samples')->findBySlug($slug)
                                    │
                                    └→ Render view with data
```

### Order Creation Flow

```
CheckoutController::processCheckout()
    │
    ├→ 1. Validate checkout data
    │     (name, email, phone, address)
    │
    ├→ 2. Calculate totals
    │     PriceCalculator::calculate($items)
    │
    ├→ 3. Create order
    │     Order::create([...])
    │
    ├→ 4. Create order items
    │     foreach($cartItems as $item)
    │         OrderItem::create([...])
    │
    ├→ 5. Handle payment
    │     if (bank_transfer)
    │         Upload screenshot
    │         Set status: pending_verification
    │
    ├→ 6. Clear cart
    │     CartItem::where('user_id', $userId)->delete()
    │
    ├→ 7. Queue email
    │     Mail::queue(new OrderConfirmationMail($order))
    │
    └→ 8. Redirect to confirmation
          redirect()->route('checkout.confirmation', $order)
```

---

## 🔧 Component Architecture

### Reusable Components

```
Blade Components:
┌────────────────────────────────┐
│ <x-app-layout>                 │ → Main public layout
│   ├── Navigation                │
│   ├── Header                    │
│   ├── Content Slot              │
│   └── Footer                    │
└────────────────────────────────┘

┌────────────────────────────────┐
│ <x-guest-layout>               │ → Guest pages
│   ├── Simple Header             │
│   ├── Content Slot              │
│   └── Simple Footer             │
└────────────────────────────────┘

┌────────────────────────────────┐
│ Floating Chat Widget           │ → Live chat
│   ├── Chat Icon                 │
│   ├── Chat Window               │
│   ├── Message List              │
│   └── Input Form                │
└────────────────────────────────┘
```

---

## 📦 Module Structure

### Feature Modules

```
Services Module:
├── Routes: /services, /services/{slug}
├── Controller: ServiceController
├── Models: Service, ServiceSample
├── Views: services/index, services/show
└── Migrations: create_services_table

Cart Module:
├── Routes: /cart/*
├── Controller: CartController
├── Model: CartItem
├── Views: cart/view
├── JavaScript: unified-cart.js
└── Migration: create_cart_items_table

Order Module:
├── Routes: /checkout, /orders
├── Controllers: CheckoutController, OrderController
├── Models: Order, OrderItem, OrderNote
├── Views: checkout/*, orders/*
├── Mail: OrderConfirmationMail
└── Migrations: create_orders_table, create_order_items_table

Chat Module:
├── Routes: /chat/*, /chatbot/*
├── Controllers: ChatController, ChatbotController
├── Models: UnifiedChat, UnifiedChatMessage
├── Views: chat/*, components/floating-chat
├── Mail: ChatNotificationMail
└── Migrations: create_unified_chats_table

Admin Module:
├── Routes: /admin/*
├── Controllers: Admin\* (11 controllers)
├── Middleware: AdminMiddleware
├── Views: admin/*
└── Models: (all models)
```

---

**Architecture Version:** 1.0
**Last Updated:** October 9, 2025


