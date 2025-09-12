# Admin/Business Operations Implementation Guide

This document outlines the implementation of a comprehensive admin system for the Al-Tahir Graphics Laravel application.

## Features Implemented

### 1. Role-Based Access Control (RBAC)
- **Spatie Laravel Permission** integration
- Three roles: `admin`, `designer`, `support`
- User impersonation functionality
- Granular permissions for all operations

### 2. Admin Panels
- **Dashboard**: Overview with key metrics and charts
- **User Management**: Create, edit, delete users, assign roles
- **Order Management**: View, edit, assign designers, add notes
- **Quote Management**: Create, approve, reject custom quotes
- **Invoice Management**: Generate PDF invoices, send via email
- **Service Management**: Manage graphic design services
- **Reporting**: Sales, orders, services, and designer analytics

### 3. Order Workflows
- Designer assignment system
- Design status tracking (pending, in_progress, review, approved, completed)
- Order notes and history
- Custom quote creation and approval

### 4. Reporting & Exports
- Sales reports with date range filters
- Order analytics and status breakdowns
- Service performance metrics
- Designer productivity reports
- CSV/Excel export functionality

### 5. Invoicing System
- PDF generation using DomPDF
- Email delivery system
- Invoice status management
- Download functionality

### 6. Audit Logging
- Complete admin action tracking
- User activity monitoring
- Change history for all entities

## Installation & Setup

### 1. Install Dependencies
```bash
composer require spatie/laravel-permission barryvdh/laravel-dompdf maatwebsite/excel
```

### 2. Publish Configurations
```bash
php artisan vendor:publish --provider="Spatie\Permission\PermissionServiceProvider"
php artisan vendor:publish --provider="Barryvdh\DomPDF\ServiceProvider"
```

### 3. Run Migrations
```bash
php artisan migrate
```

### 3.1 Link Storage
```bash
php artisan storage:link
```

### 4. Seed Database
```bash
php artisan db:seed --class=RolePermissionSeeder
php artisan db:seed --class=AdminUserSeeder
```

### 5. Register Middleware
Add to `app/Http/Kernel.php`:
```php
protected $routeMiddleware = [
    // ... existing middleware
    'admin' => \App\Http\Middleware\AdminMiddleware::class,
    'designer' => \App\Http\Middleware\DesignerMiddleware::class,
];
```

### 6. Include Admin Routes
Add to `routes/web.php`:
```php
require __DIR__.'/admin.php';
```

## Environment Configuration

### Required Environment Variables
```env
# Mail configuration for invoice delivery
MAIL_MAILER=smtp
MAIL_HOST=your-smtp-host
MAIL_PORT=587
MAIL_USERNAME=your-email
MAIL_PASSWORD=your-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@yourdomain.com
MAIL_FROM_NAME="Your Company Name"

# File storage for PDFs
FILESYSTEM_DISK=public
```

## Default Admin Credentials

After running the seeders, you can login with:

- **Admin User**: admin@example.com / password
- **Designer User**: designer@example.com / password  
- **Support User**: support@example.com / password

## Usage Examples

### Accessing Admin Panel
Navigate to `/admin` after logging in with admin/support role.

### Creating a New User
```php
$user = User::create([
    'name' => 'John Doe',
    'email' => 'john@example.com',
    'password' => Hash::make('password')
]);

$user->assignRole('designer');
```

### Assigning Designer to Order
```php
$order->update([
    'assigned_designer_id' => $designerId,
    'design_status' => 'pending',
    'design_due_date' => now()->addDays(7)
]);
```

### Generating Invoice PDF
```php
$pdf = Pdf::loadView('admin.invoices.pdf', compact('invoice'));
return $pdf->download($invoice->invoice_number . '.pdf');
```

## API Endpoints

### Admin Routes
- `GET /admin` - Dashboard
- `GET /admin/users` - User management
- `GET /admin/orders` - Order management
- `GET /admin/quotes` - Quote management
- `GET /admin/invoices` - Invoice management
- `GET /admin/reports` - Reporting system

### Export Endpoints
- `GET /admin/orders/export?format=csv` - Export orders to CSV
- `GET /admin/reports/export-sales` - Export sales report

## Testing the System

### 1. Test Admin Access
```bash
# Login as admin
curl -X POST /login -d "email=admin@example.com&password=password"

# Access admin panel
curl -X GET /admin
```

### 2. Test User Impersonation
```bash
# Impersonate a user
curl -X POST /admin/users/{user_id}/impersonate

# Stop impersonating
curl -X POST /admin/stop-impersonating
```

### 3. Test Order Assignment
```bash
# Assign designer to order
curl -X POST /admin/orders/{order_id}/assign-designer \
  -d "assigned_designer_id={designer_id}&design_due_date=2025-09-02"
```

## Troubleshooting

### Common Issues

1. **Permission Denied Errors**
   - Ensure user has correct role assigned
   - Check middleware configuration
   - Verify permission cache is cleared

2. **PDF Generation Fails**
   - Install required PHP extensions (GD, mbstring)
   - Check storage permissions
   - Verify DomPDF configuration

3. **Role Assignment Issues**
   - Clear permission cache: `php artisan permission:cache-reset`
   - Re-run role seeder
   - Check database table structure

### Debug Commands
```bash
# Clear all caches
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

# Reset permissions
php artisan permission:cache-reset

# Check user roles
php artisan tinker
>>> $user = User::find(1);
>>> $user->getRoleNames();
```

## Security Considerations

1. **Role-Based Access**: All admin routes are protected by middleware
2. **Audit Logging**: All admin actions are logged with user and IP tracking
3. **Input Validation**: All forms include proper validation rules
4. **CSRF Protection**: All forms include CSRF tokens
5. **User Impersonation**: Limited to non-admin users only

## Performance Optimization

1. **Database Indexing**: Proper indexes on frequently queried fields
2. **Eager Loading**: Relationships loaded efficiently to prevent N+1 queries
3. **Caching**: Permission cache enabled for role checks
4. **Pagination**: Large datasets paginated for better performance

## Future Enhancements

1. **Real-time Notifications**: WebSocket integration for live updates
2. **Advanced Analytics**: More detailed reporting and charts
3. **Workflow Automation**: Automated designer assignment based on workload
4. **Mobile Admin App**: React Native or Flutter mobile application
5. **API Rate Limiting**: Enhanced API security and monitoring

## Support

For technical support or questions about the implementation:
- Check the Laravel documentation
- Review Spatie Permission package documentation
- Examine the audit logs for debugging
- Use Laravel Telescope for detailed request monitoring
