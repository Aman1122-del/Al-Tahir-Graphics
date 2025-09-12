# Live Chat System Implementation

A comprehensive real-time chat system for Laravel with role-based access control, file uploads, and admin management.

## Features

### 🚀 Core Chat Features
- **Real-time messaging** using Pusher/Laravel WebSockets
- **File uploads** (images, documents) with 10MB limit
- **Unread message tracking** with visual indicators
- **Message history** and conversation management
- **Role-based access control** (customers ↔ admin/support)

### 👥 User Management
- **Customer Support**: Regular users can chat with admin/support
- **Admin Panel**: Full chat management and monitoring
- **Support Staff**: Can handle multiple customer conversations
- **User impersonation** for admin troubleshooting

### 🎨 User Interface
- **Main Chat Interface**: Full-featured chat at `/chat`
- **Floating Widget**: Compact chat widget for any page
- **Admin Dashboard**: Comprehensive chat management
- **Responsive Design**: Works on desktop and mobile
- **Tailwind CSS + Alpine.js**: Modern, interactive UI

### 📊 Admin Features
- **Chat Statistics**: Message counts, user activity
- **Conversation Management**: View all active chats
- **Export Functionality**: CSV/Excel export of chat data
- **User Search**: Find and manage chat users
- **Real-time Monitoring**: Live updates and notifications

## Installation

### 1. Install Dependencies
```bash
composer install
npm install
```

### 2. Environment Configuration
Add these variables to your `.env` file:

```env
# Broadcasting
BROADCAST_DRIVER=pusher

# Pusher Configuration
PUSHER_APP_ID=your_app_id
PUSHER_APP_KEY=your_app_key
PUSHER_APP_SECRET=your_app_secret
PUSHER_APP_CLUSTER=mt1

# WebSockets (Optional - for self-hosted WebSockets)
LARAVEL_WEBSOCKETS_PORT=6001
LARAVEL_WEBSOCKETS_SSL_LOCAL_CERT=null
LARAVEL_WEBSOCKETS_SSL_LOCAL_PK=null
LARAVEL_WEBSOCKETS_SSL_PASSPHRASE=null
```

### 3. Run Migrations
```bash
php artisan migrate
```

### 4. Seed Database
```bash
php artisan db:seed --class=RolePermissionSeeder
php artisan db:seed --class=AdminUserSeeder
```

### 5. Create Storage Link
```bash
php artisan storage:link
```

### 6. Compile Assets
```bash
npm run build
```

## Usage

### For Customers
1. **Access Chat**: Navigate to `/chat` or use the floating widget
2. **Start Conversation**: Automatically connects to available support staff
3. **Send Messages**: Type and send text messages
4. **File Uploads**: Attach files using the paperclip icon
5. **Real-time Updates**: Messages appear instantly

### For Support Staff
1. **Admin Panel**: Access `/admin/chat`
2. **View Conversations**: See all active customer chats
3. **Respond**: Click on any user to start chatting
4. **Monitor Activity**: Track message statistics and user engagement

### For Administrators
1. **Full Access**: All support features plus system management
2. **User Management**: Assign roles and manage permissions
3. **Export Data**: Download chat logs and statistics
4. **System Monitoring**: Track overall chat system performance

## API Endpoints

### Chat Routes
- `GET /chat` - Main chat interface
- `POST /chat/send` - Send a message
- `GET /chat/messages/{user_id}` - Fetch conversation
- `POST /chat/mark-read` - Mark messages as read
- `GET /chat/unread-count` - Get unread message count
- `GET /chat/users` - Get available chat users

### Admin Chat Routes
- `GET /admin/chat` - Admin chat dashboard
- `GET /admin/chat/{userId}` - View specific conversation
- `GET /admin/chat/statistics` - Get chat statistics
- `GET /admin/chat/search-users` - Search chat users
- `GET /admin/chat/export` - Export chat data

## Components

### Floating Chat Widget
Include on any page:
```blade
@include('components.floating-chat')
```

### Main Chat Interface
Full chat experience:
```blade
@include('chat.index')
```

### Admin Chat Dashboard
Admin management:
```blade
@include('admin.chat.index')
```

## File Storage

Chat files are stored in `storage/app/public/chat_uploads/` and are accessible via:
- **Public URL**: `/storage/chat_uploads/filename.ext`
- **File Size Limit**: 10MB per file
- **Supported Types**: All file types (images, documents, etc.)

## Real-time Features

### Pusher Integration
- **Channels**: `chat-channel`
- **Events**: `new-message`
- **Data**: Message content, sender info, receiver ID

### WebSockets (Alternative)
- **Port**: 6001 (configurable)
- **Dashboard**: `/laravel-websockets` (if enabled)
- **Self-hosted**: No external dependencies

## Security Features

### Role-Based Access
- **Customers**: Can only chat with admin/support
- **Support**: Can access all customer conversations
- **Admin**: Full system access and management

### Message Validation
- **Input Sanitization**: XSS protection
- **File Validation**: Type and size restrictions
- **User Authorization**: Prevents unauthorized access

## Customization

### Styling
- **Tailwind CSS**: Easy theme customization
- **Component Classes**: Reusable design patterns
- **Responsive Breakpoints**: Mobile-first approach

### Functionality
- **Alpine.js**: Interactive behavior management
- **Event Handling**: Customizable chat events
- **API Integration**: Extensible backend endpoints

## Troubleshooting

### Common Issues

1. **Pusher Connection Failed**
   - Check environment variables
   - Verify app credentials
   - Test network connectivity

2. **File Upload Errors**
   - Check storage permissions
   - Verify file size limits
   - Ensure storage link exists

3. **Real-time Not Working**
   - Check broadcasting configuration
   - Verify JavaScript console for errors
   - Test Pusher/WebSockets connection

### Debug Mode
Enable debug logging in `.env`:
```env
APP_DEBUG=true
LOG_LEVEL=debug
```

## Performance

### Optimization Tips
- **Message Pagination**: Load messages in chunks
- **File Compression**: Optimize image uploads
- **Caching**: Cache user lists and statistics
- **Database Indexing**: Index message queries

### Monitoring
- **Message Queue**: Handle high-volume scenarios
- **Memory Usage**: Monitor WebSocket connections
- **Database Performance**: Track query execution times

## Future Enhancements

### Planned Features
- **Voice Messages**: Audio recording support
- **Video Calls**: WebRTC integration
- **Chat Bots**: AI-powered responses
- **Multi-language**: Internationalization support
- **Advanced Analytics**: Detailed reporting
- **Mobile App**: Native mobile support

### Integration Possibilities
- **CRM Systems**: Customer relationship management
- **Help Desk**: Ticket system integration
- **Analytics**: Google Analytics, Mixpanel
- **Notifications**: Email, SMS, push notifications

## Support

For technical support or feature requests:
- **Documentation**: Check this README first
- **Issues**: Report bugs via GitHub issues
- **Community**: Laravel community forums
- **Professional**: Contact development team

## License

This chat system is part of the Al-Tahir Graphics Laravel application and follows the same licensing terms.

---

**Note**: This system requires proper server configuration for WebSockets and file uploads. Ensure your hosting environment supports these features.
