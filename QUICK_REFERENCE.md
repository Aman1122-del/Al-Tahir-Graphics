# Al-Tahir Graphics - Quick Reference Guide

## 🚀 Quick Start

### Development Server
```bash
# Start all services (recommended)
composer dev

# Or manually:
php artisan serve                    # App server
php artisan queue:listen             # Queue worker
npm run dev                          # Vite dev server
```

### Access Points
- **Frontend:** http://localhost:8000
- **Admin Panel:** http://localhost:8000/admin
- **Login:** http://localhost:8000/login

### Default Credentials
```
Admin Account:
Email: admin@altahir.local
Password: [Check AdminUserSeeder.php]
```

---

## 📁 Project Structure at a Glance

```
app/
├── Http/Controllers/          # Business logic
│   ├── Admin/                # Admin controllers (11 files)
│   ├── ServiceController     # Public services
│   ├── CartController        # Shopping cart
│   ├── CheckoutController    # Order processing
│   └── ChatController        # Live chat
├── Models/                   # Database models (24 files)
│   ├── User                  # Users
│   ├── Service               # Services catalog
│   ├── Order                 # Orders
│   ├── UnifiedChat           # Chat system
│   └── ...
├── Mail/                     # Email templates
└── Helpers/                  # Utility classes

resources/views/
├── pages/                    # Public pages
├── admin/                    # Admin interface
├── components/               # Reusable components
└── layouts/                  # Layouts

routes/
├── web.php                   # Public routes
├── admin.php                 # Admin routes (/admin/*)
├── auth.php                  # Auth routes
└── chat.php                  # Chat routes
```

---

## 🔑 Key Routes

### Public Routes
```
/                              Homepage
/services                      Service catalog
/services/{slug}               Service details
/cart                          Shopping cart
/checkout                      Checkout page
/login                         User login
/register                      User registration
```

### Admin Routes (Requires Auth + Admin)
```
/admin                         Dashboard
/admin/orders                  Order management
/admin/users                   User management
/admin/services                Service CRUD
/admin/quotes                  Quote management
/admin/invoices                Invoice management
/admin/reports                 Business reports
/admin/chat                    Chat management
/admin/chat/unified            Unified chat system
```

### Chat Routes (Requires Auth)
```
/chat                          User chat interface
/chatbot/start                 Start chatbot session
/chatbot/message               Send message
```

---

## 🗄️ Key Database Tables

### Core Tables
```sql
users                    # System users
services                 # Printing services
service_samples          # Design samples
cart_items              # Shopping cart
orders                  # Customer orders
order_items             # Order line items
```

### Chat Tables
```sql
unified_chats           # Chat sessions
unified_chat_messages   # Chat messages
chat_participants       # Chat users
chat_settings           # Chat configuration
```

### Business Tables
```sql
quotes                  # Custom quotes
invoices                # Generated invoices
coupons                 # Discount coupons
order_notes             # Order history
audit_logs              # Activity tracking
```

---

## 💼 Key Models & Relationships

### User Model
```php
$user->orders           // Orders placed by user
$user->cartItems        // Cart items
$user->chats            // Chats created
$user->assignedOrders   // Orders assigned to designer
$user->isAdmin()        // Check if admin
```

### Service Model
```php
$service->samples       // Service samples
$service->formatted_price  // "PKR 5,000"
$service->active()      // Scope: active services
$service->featured()    // Scope: featured services
```

### Order Model
```php
$order->orderItems      // Items in order
$order->user            // Customer
$order->assignedDesigner  // Assigned designer
$order->orderNotes      // Order history
$order->invoice         // Generated invoices
$order->isPaid()        // Payment status check
$order->generateOrderNumber()  // ATG20250109XXXXXX
```

### UnifiedChat Model
```php
$chat->messages         // All messages
$chat->participants     // Chat participants
$chat->creator          // User who started chat
$chat->assignedUser     // Admin/support assigned
$chat->getUnreadCountForUser($userId)
$chat->markAsReadForUser($userId)
```

---

## 🎨 Frontend Assets

### CSS Files
```
resources/css/app.css              # Main styles
resources/css/product-category.css # Product styles
```

### JavaScript Files
```
resources/js/app.js                # Main JS
resources/js/bootstrap.js          # Bootstrap (Axios, Echo)
resources/js/unified-cart.js       # Cart functionality
```

### Blade Components
```
@props                             # Component props
<x-app-layout>                     # Main layout
<x-guest-layout>                   # Guest layout
<x-admin-layout>                   # Admin layout (if exists)
```

---

## 🔧 Common Artisan Commands

### Database
```bash
php artisan migrate                # Run migrations
php artisan migrate:fresh --seed   # Fresh DB with seed data
php artisan db:seed                # Seed data only
php artisan migrate:rollback       # Rollback last migration
```

### Cache
```bash
php artisan cache:clear            # Clear app cache
php artisan config:clear           # Clear config cache
php artisan route:clear            # Clear route cache
php artisan view:clear             # Clear view cache
php artisan optimize:clear         # Clear all caches
```

### Development
```bash
php artisan route:list             # List all routes
php artisan tinker                 # Interactive console
php artisan test                   # Run tests
php artisan make:controller Name   # Create controller
php artisan make:model Name        # Create model
php artisan make:migration name    # Create migration
```

### Production
```bash
php artisan optimize               # Optimize for production
php artisan config:cache           # Cache config
php artisan route:cache            # Cache routes
php artisan view:cache             # Cache views
```

### Storage
```bash
php artisan storage:link           # Create storage symlink
```

---

## 📊 Order Status Reference

### Order Status
```
pending      → Awaiting payment
paid         → Payment confirmed
processing   → Being worked on
completed    → Order finished
cancelled    → Order cancelled
```

### Payment Status
```
pending              → Not yet paid
pending_verification → Screenshot uploaded
paid                 → Payment confirmed
failed               → Payment rejected
```

### Design Status
```
pending      → Not started
in_progress  → Designer working
completed    → Design finished
approved     → Customer approved
```

---

## 👥 User Roles

### Roles
```
Super Admin  → Full system access
Admin        → Business operations
Designer     → Design work
Support      → Customer service
Customer     → Self-service
```

### Permission Checks
```php
$user->isAdmin()           // Check if admin
$user->hasRole('admin')    // Check specific role
$user->can('manage users') // Check permission
$user->canManageOrders()   // Helper method
```

---

## 📧 Email Notifications

### Mail Classes
```php
OrderConfirmationMail    # To customer on order
OrderNotificationMail    # To admin for new order
ChatNotificationMail     # For chat messages
```

### Sending Email
```php
Mail::to($user)->send(new OrderConfirmationMail($order));
```

---

## 🔐 Middleware

### Available Middleware
```php
'auth'           // Require authentication
'admin'          // Require admin role
'designer'       // Require designer role
'guest'          // Only for non-authenticated
'verified'       // Email verified
```

### Usage in Routes
```php
Route::get('/admin')->middleware(['auth', 'admin']);
```

---

## 🧪 Testing

### Run Tests
```bash
php artisan test                      # All tests
php artisan test --filter=ChatSystem  # Specific test
php artisan test --coverage           # With coverage
```

### Test Structure
```
tests/
├── Feature/                          # Feature tests
│   ├── ChatSystemTest.php
│   ├── OrderManagementTest.php
│   └── ...
└── Unit/                             # Unit tests
    └── PriceCalculatorTest.php
```

---

## 📦 Key Dependencies

### PHP Packages
```json
"laravel/framework": "^12.0"           // Framework
"laravel/breeze": "^2.3"               // Authentication
"spatie/laravel-permission": "^6.21"   // Permissions
"barryvdh/laravel-dompdf": "3.1.1"    // PDF generation
"maatwebsite/excel": "^3.1"            // Excel export
"pusher/pusher-php-server": "^7.2"     // Real-time (unused)
```

### JavaScript Packages
```json
"alpinejs": "^3.4.2"                   // JS framework
"tailwindcss": "^3.1.0"                // CSS framework
"axios": "^1.11.0"                     // HTTP client
"vite": "^7.0.4"                       // Build tool
```

---

## 🐛 Debugging

### Log Files
```
storage/logs/laravel.log              # Application logs
storage/logs/laravel-YYYY-MM-DD.log   # Daily logs
```

### Debug Mode
```env
# .env file
APP_DEBUG=true                        # Enable debug mode
LOG_LEVEL=debug                       # Detailed logs
```

### Common Issues

**Issue: 404 on admin routes**
```bash
php artisan route:clear
php artisan optimize:clear
```

**Issue: Storage files not accessible**
```bash
php artisan storage:link
```

**Issue: Permission errors**
```bash
# Linux/Mac
chmod -R 775 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache

# Windows - run as administrator
icacls "storage" /grant Everyone:(OI)(CI)F /T
```

**Issue: Chat not updating**
- Check database polling is running
- Verify JavaScript console for errors
- Check database connection

---

## 📈 Performance Tips

### Database Optimization
```php
// Use eager loading
$orders = Order::with(['user', 'orderItems'])->get();

// Use select to limit columns
$orders = Order::select('id', 'order_number', 'total_amount')->get();

// Add indexes in migrations
$table->index('order_status');
```

### Caching
```php
// Cache expensive queries
$services = Cache::remember('featured-services', 3600, function () {
    return Service::featured()->active()->get();
});
```

### Query Optimization
```bash
# Enable query log
php artisan debugbar:publish  # If using debugbar

# Check slow queries
tail -f storage/logs/laravel.log | grep "SELECT"
```

---

## 🔄 Common Workflows

### Adding a New Service
```php
1. Create service in admin panel
2. Upload service image
3. Add samples (optional)
4. Set pricing
5. Mark as active
6. Featured (optional)
```

### Processing an Order
```php
1. View order in /admin/orders
2. Verify payment (if needed)
3. Assign to designer
4. Update design status
5. Generate invoice
6. Send invoice to customer
7. Mark as completed
```

### Responding to Chat
```php
1. Go to /admin/chat/unified
2. Click on active chat
3. View conversation
4. Type reply
5. Send message
6. Mark as resolved (optional)
```

---

## 🚨 Emergency Commands

### Reset Admin Password
```bash
php artisan tinker
$admin = User::where('email', 'admin@example.com')->first();
$admin->password = Hash::make('newpassword');
$admin->save();
```

### Clear All Caches
```bash
php artisan optimize:clear
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
```

### Backup Database
```bash
# SQLite
cp database/database.sqlite database/backup-$(date +%Y%m%d).sqlite

# MySQL
mysqldump -u root database_name > backup.sql
```

### Restore Database
```bash
# SQLite
cp database/backup-20250109.sqlite database/database.sqlite

# MySQL
mysql -u root database_name < backup.sql
```

---

## 📞 Quick Reference Links

### Documentation
- Full Overview: `PROJECT_OVERVIEW.md`
- Chat Implementation: `IMPLEMENTATION_COMPLETE.md`
- Admin Features: `IMPLEMENTATION_SUMMARY.md`
- Laravel Docs: https://laravel.com/docs

### File Locations
- Models: `app/Models/`
- Controllers: `app/Http/Controllers/`
- Views: `resources/views/`
- Routes: `routes/`
- Migrations: `database/migrations/`
- Config: `config/`

---

## 💡 Tips & Tricks

### Useful Aliases (add to .bashrc or .zshrc)
```bash
alias pa='php artisan'
alias pat='php artisan test'
alias pam='php artisan migrate'
alias pac='php artisan cache:clear'
alias sail='vendor/bin/sail'
```

### VS Code Extensions
- Laravel Extra Intellisense
- Laravel Blade Snippets
- PHP Intelephense
- Tailwind CSS IntelliSense

### Useful Tinker Commands
```php
// Check orders count
Order::count()

// Get latest orders
Order::latest()->take(5)->get()

// Find user by email
User::where('email', 'test@example.com')->first()

// Clear a specific cache key
Cache::forget('featured-services')

// Get all admin users
User::where('is_admin', true)->get()
```

---

**Quick Reference Version:** 1.0
**Last Updated:** October 9, 2025


