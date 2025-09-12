@if(auth()->check())
<div x-data="floatingChat" class="fixed bottom-4 right-4 z-50">
    <!-- Chat Toggle Button -->
    <div x-show="!isOpen" class="relative">
        <button @click="toggleChat()" 
                class="w-16 h-16 bg-blue-500 hover:bg-blue-600 text-white rounded-full shadow-lg flex items-center justify-center transition-all duration-300 hover:scale-110">
            <svg x-show="unreadCount === 0" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
            </svg>
            <svg x-show="unreadCount > 0" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
            </svg>
        </button>
        
        <!-- Unread Badge -->
        <div x-show="unreadCount > 0" 
             class="absolute -top-2 -right-2 bg-red-500 text-white text-xs rounded-full h-6 w-6 flex items-center justify-center animate-pulse">
            <span x-text="unreadCount > 99 ? '99+' : unreadCount"></span>
        </div>
    </div>

    <!-- Chat Window -->
    <div x-show="isOpen" 
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 scale-95"
         x-transition:enter-end="opacity-100 scale-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100 scale-100"
         x-transition:leave-end="opacity-0 scale-95"
         class="w-80 h-96 bg-white rounded-lg shadow-xl border border-gray-200 flex flex-col">
        
        <!-- Chat Header -->
        <div class="bg-blue-500 text-white p-4 rounded-t-lg flex items-center justify-between">
            <div class="flex items-center">
                <div class="w-8 h-8 bg-white bg-opacity-20 rounded-full flex items-center justify-center mr-3">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                    </svg>
                </div>
                <div>
                    <h3 class="font-medium">Chat Support</h3>
                    <p class="text-sm opacity-90">We're here to help!</p>
                </div>
            </div>
            <button @click="toggleChat()" class="text-white hover:text-gray-200">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>

        <!-- Messages Area -->
        <div class="flex-1 p-4 overflow-y-auto bg-gray-50" id="floating-messages">
            <template x-for="message in messages" :key="message.id">
                <div class="mb-3" :class="{ 'text-right': message.sender_id === currentUserId }">
                    <div class="inline-block max-w-xs px-3 py-2 rounded-lg text-sm"
                         :class="{ 
                             'bg-blue-500 text-white': message.sender_id === currentUserId,
                             'bg-white text-gray-900 border border-gray-200': message.sender_id !== currentUserId
                         }">
                        <p x-text="message.message"></p>
                        
                        <!-- File Attachment -->
                        <div x-show="message.file_path" class="mt-2">
                            <a :href="message.file_url" 
                               target="_blank"
                               class="inline-flex items-center text-xs underline"
                               :class="{ 
                                   'text-blue-200': message.sender_id === currentUserId,
                                   'text-blue-600': message.sender_id !== currentUserId
                               }">
                                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"></path>
                                </svg>
                                <span x-text="message.file_name"></span>
                            </a>
                        </div>
                        
                        <p class="text-xs mt-1 opacity-75" x-text="formatTime(message.created_at)"></p>
                    </div>
                </div>
            </template>
            
            <!-- Welcome Message -->
            <div x-show="messages.length === 0" class="text-center text-gray-500 py-8">
                <svg class="w-12 h-12 mx-auto mb-3 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                </svg>
                <p class="text-sm">Hello! How can we help you today?</p>
            </div>
        </div>

        <!-- Message Input -->
        <div class="p-4 border-t border-gray-200 bg-white">
            <form @submit.prevent="sendMessage()" class="flex space-x-2">
                <!-- File Upload -->
                <div class="flex-shrink-0">
                    <label for="floating-file-upload" class="cursor-pointer text-gray-400 hover:text-gray-600">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"></path>
                        </svg>
                    </label>
                    <input id="floating-file-upload" type="file" class="hidden" @change="handleFileSelect($event)">
                </div>
                
                <!-- Message Input -->
                <div class="flex-1">
                    <input type="text" 
                           x-model="newMessage" 
                           placeholder="Type your message..."
                           class="w-full px-3 py-2 text-sm border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                           :disabled="sending">
                </div>
                
                <!-- Send Button -->
                <button type="submit" 
                        class="px-3 py-2 bg-blue-500 text-white text-sm rounded-md hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-500 disabled:opacity-50"
                        :disabled="!newMessage.trim() || sending">
                    <span x-show="!sending">Send</span>
                    <span x-show="sending">...</span>
                </button>
            </form>
            
            <!-- File Preview -->
            <div x-show="selectedFile" class="mt-2 p-2 bg-gray-100 rounded-md">
                <div class="flex items-center justify-between">
                    <span class="text-xs text-gray-600 truncate" x-text="selectedFile.name"></span>
                    <button @click="selectedFile = null" class="text-red-500 hover:text-red-700">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Alpine.js Logic for Floating Chat -->
<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('floatingChat', () => ({
        isOpen: false,
        messages: [],
        newMessage: '',
        selectedFile: null,
        sending: false,
        unreadCount: 0,
        currentUserId: {{ auth()->id() }},
        supportUserId: null,
        
        init() {
            this.loadUnreadCount();
            this.initializePusher();
            this.findSupportUser();
        },
        
        async findSupportUser() {
            try {
                const response = await fetch('/chat/users');
                const data = await response.json();
                
                if (data.success && data.users.length > 0) {
                    // Find first admin/support user
                    const supportUser = data.users.find(user => 
                        user.roles && (user.roles.includes('admin') || user.roles.includes('support'))
                    );
                    
                    if (supportUser) {
                        this.supportUserId = supportUser.id;
                        this.loadMessages();
                    }
                }
            } catch (error) {
                console.error('Error finding support user:', error);
            }
        },
        
        toggleChat() {
            this.isOpen = !this.isOpen;
            if (this.isOpen) {
                this.loadMessages();
                this.markAsRead();
            }
        },
        
        async loadMessages() {
            if (!this.supportUserId) return;
            
            try {
                const response = await fetch(`/chat/messages/${this.supportUserId}`);
                const data = await response.json();
                
                if (data.success) {
                    this.messages = data.messages;
                    this.scrollToBottom();
                }
            } catch (error) {
                console.error('Error loading messages:', error);
            }
        },
        
        async sendMessage() {
            if (!this.newMessage.trim() && !this.selectedFile) return;
            if (!this.supportUserId) return;
            
            this.sending = true;
            
            try {
                const formData = new FormData();
                formData.append('receiver_id', this.supportUserId);
                formData.append('message', this.newMessage);
                
                if (this.selectedFile) {
                    formData.append('file', this.selectedFile);
                }
                
                const response = await fetch('/chat/send', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: formData
                });
                
                const data = await response.json();
                
                if (data.success) {
                    this.messages.push(data.message);
                    this.newMessage = '';
                    this.selectedFile = null;
                    this.scrollToBottom();
                    this.loadUnreadCount();
                }
            } catch (error) {
                console.error('Error sending message:', error);
            } finally {
                this.sending = false;
            }
        },
        
        async markAsRead() {
            if (!this.supportUserId) return;
            
            try {
                await fetch('/chat/mark-read', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({ sender_id: this.supportUserId })
                });
                
                this.unreadCount = 0;
            } catch (error) {
                console.error('Error marking messages as read:', error);
            }
        },
        
        handleFileSelect(event) {
            const file = event.target.files[0];
            if (file) {
                if (file.size > 10 * 1024 * 1024) { // 10MB limit
                    alert('File size must be less than 10MB');
                    event.target.value = '';
                    return;
                }
                this.selectedFile = file;
            }
        },
        
        async loadUnreadCount() {
            try {
                const response = await fetch('/chat/unread-count');
                const data = await response.json();
                
                if (data.success) {
                    this.unreadCount = data.count;
                }
            } catch (error) {
                console.error('Error loading unread count:', error);
            }
        },
        
        scrollToBottom() {
            this.$nextTick(() => {
                const container = document.getElementById('floating-messages');
                if (container) {
                    container.scrollTop = container.scrollHeight;
                }
            });
        },
        
        formatTime(timestamp) {
            const date = new Date(timestamp);
            const now = new Date();
            const diff = now - date;
            
            if (diff < 60000) return 'Just now';
            if (diff < 3600000) return Math.floor(diff / 60000) + 'm ago';
            if (diff < 86400000) return Math.floor(diff / 3600000) + 'h ago';
            return date.toLocaleDateString();
        },
        
        initializePusher() {
            // Initialize Pusher for real-time updates
            if (typeof Pusher !== 'undefined') {
                const pusher = new Pusher('{{ config("broadcasting.connections.pusher.key") }}', {
                    cluster: '{{ config("broadcasting.connections.pusher.options.cluster") }}'
                });
                
                const channel = pusher.subscribe('chat-channel');
                channel.bind('new-message', (data) => {
                    if (data.receiver_id === this.currentUserId || data.message.sender_id === this.currentUserId) {
                        // Reload messages if chat is open
                        if (this.isOpen) {
                            this.loadMessages();
                        }
                        this.loadUnreadCount();
                    }
                });
            }
        }
    }));
});
</script>
@endif
