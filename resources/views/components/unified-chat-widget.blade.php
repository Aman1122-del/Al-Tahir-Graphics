<div x-data="unifiedChatWidget()" class="fixed bottom-4 right-4 z-50 chat-widget">
    <!-- Chat Toggle Button with Unread Badge -->
    <div class="relative">
        <button @click="toggleChat()" 
                class="bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 text-white rounded-full p-4 shadow-2xl transition-all duration-300 hover:scale-105 focus:outline-none focus:ring-4 focus:ring-blue-300">
            <svg x-show="!isOpen" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
            </svg>
            <svg x-show="isOpen" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
        </button>
        
        <!-- Unread Badge -->
        <div x-show="unreadCount > 0" 
             x-text="unreadCount > 99 ? '99+' : unreadCount"
             class="absolute -top-2 -right-2 bg-red-500 text-white text-xs font-bold rounded-full h-6 w-6 flex items-center justify-center unread-badge shadow-lg">
        </div>
    </div>

    <!-- Chat Window -->
    <div x-show="isOpen" 
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 transform scale-95 translate-y-4"
         x-transition:enter-end="opacity-100 transform scale-100 translate-y-0"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100 transform scale-100 translate-y-0"
         x-transition:leave-end="opacity-0 transform scale-95 translate-y-4"
         class="absolute bottom-16 right-0 w-96 bg-white rounded-2xl shadow-2xl border border-gray-200 overflow-hidden chat-window">
        
        <!-- Chat Header -->
        <div class="bg-gradient-to-r from-blue-600 to-blue-700 text-white p-4">
            <div class="flex justify-between items-center">
                <div class="flex items-center space-x-3">
                    <!-- Admin Avatar -->
                    <div class="w-10 h-10 bg-white bg-opacity-20 rounded-full flex items-center justify-center">
                        <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/>
                        </svg>
                    </div>
                    <div>
                        <h3 class="font-semibold text-lg">Support Team</h3>
                        <div class="flex items-center space-x-2">
                            <div class="w-2 h-2 bg-green-400 rounded-full animate-pulse"></div>
                            <span class="text-sm opacity-90">Online</span>
                        </div>
                    </div>
                </div>
                <button @click="toggleChat()" 
                        class="text-white hover:bg-white hover:bg-opacity-20 rounded-full p-2 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
        </div>

        <!-- Chat Messages -->
        <div class="h-96 overflow-y-auto bg-gray-50" id="chatMessages">
            <div class="p-4 space-y-4">
                <!-- Welcome Message -->
                <div x-show="messages.length === 0" class="text-center text-gray-500 py-8">
                    <div class="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                        </svg>
                    </div>
                    <p class="text-sm">Start a conversation with our support team</p>
                </div>

                <!-- Messages -->
                <template x-for="message in messages" :key="message.id">
                    <div class="flex" :class="isUserMessage(message) ? 'justify-end' : 'justify-start'">
                        <div class="flex max-w-xs lg:max-w-md" :class="isUserMessage(message) ? 'flex-row-reverse' : 'flex-row'">
                            <!-- Admin Avatar (only for admin messages) -->
                            <div x-show="!isUserMessage(message)" class="flex-shrink-0 mr-3">
                                <div class="w-8 h-8 bg-blue-600 rounded-full flex items-center justify-center">
                                    <svg class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/>
                                    </svg>
                                </div>
                            </div>

                            <!-- Message Bubble -->
                            <div class="relative">
                                <!-- Admin Badge (only for admin messages) -->
                                <div x-show="!isUserMessage(message)" class="flex items-center space-x-2 mb-1">
                                    <span class="text-xs font-semibold text-blue-600 bg-blue-100 px-2 py-1 rounded-full">
                                        Admin
                                    </span>
                                    <span class="text-xs text-gray-500" x-text="formatTime(message.created_at)"></span>
                                </div>

                                <!-- Message Content -->
                                <div class="px-4 py-3 rounded-2xl shadow-sm message-bubble" 
                                     :class="isUserMessage(message) 
                                        ? 'bg-blue-600 text-white rounded-br-md' 
                                        : 'bg-white text-gray-800 border border-gray-200 rounded-bl-md'">
                                    
                                    <!-- Message Text -->
                                    <div class="text-sm leading-relaxed" x-text="message.message"></div>
                                    
                                    <!-- File Attachment -->
                                    <div x-show="message.file_path" class="mt-2">
                                        <a :href="'/storage/' + message.file_path" target="_blank" 
                                           class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium transition-colors"
                                           :class="isUserMessage(message) 
                                               ? 'bg-blue-700 text-blue-100 hover:bg-blue-800' 
                                               : 'bg-gray-100 text-gray-700 hover:bg-gray-200'">
                                            <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"></path>
                                            </svg>
                                            <span x-text="message.file_name"></span>
                                        </a>
                                    </div>
                                </div>

                                <!-- Message Status Indicators (only for user messages) -->
                                <div x-show="isUserMessage(message)" class="flex items-center justify-end space-x-1 mt-1">
                                    <span class="text-xs text-gray-500" x-text="formatTime(message.created_at)"></span>
                                    <!-- Sent/Delivered/Read indicators -->
                                    <div class="flex items-center space-x-1">
                                        <svg class="w-3 h-3 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                        </svg>
                                        <svg class="w-3 h-3 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                        </svg>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </template>

                <!-- Typing Indicator -->
                <div x-show="isTyping" class="flex justify-start">
                    <div class="flex items-center space-x-2 bg-white border border-gray-200 rounded-2xl px-4 py-3 shadow-sm">
                        <div class="w-8 h-8 bg-blue-600 rounded-full flex items-center justify-center">
                            <svg class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/>
                            </svg>
                        </div>
                        <div class="flex space-x-1">
                            <div class="w-2 h-2 bg-gray-400 rounded-full typing-dot"></div>
                            <div class="w-2 h-2 bg-gray-400 rounded-full typing-dot"></div>
                            <div class="w-2 h-2 bg-gray-400 rounded-full typing-dot"></div>
                        </div>
                        <span class="text-xs text-gray-500">Admin is typing...</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Chat Input -->
        <div class="bg-white border-t border-gray-200 p-4">
            <!-- File Upload Preview -->
            <div x-show="selectedFile" class="mb-3 p-2 file-preview rounded-lg">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-2">
                        <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"></path>
                        </svg>
                        <span class="text-sm text-blue-800" x-text="selectedFile?.name"></span>
                    </div>
                    <button @click="selectedFile = null" class="text-blue-600 hover:text-blue-800">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
            </div>

            <form @submit.prevent="sendMessage()" class="flex items-end space-x-2">
                <!-- File Upload Button -->
                <button type="button" @click="document.getElementById('fileInput').click()"
                        class="flex-shrink-0 p-2 text-gray-500 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"></path>
                    </svg>
                </button>
                
                <!-- Hidden File Input -->
                <input type="file" id="fileInput" @change="handleFileSelect" 
                       accept="image/*,.pdf,.doc,.docx,.txt" class="hidden">

                <!-- Message Input -->
                <div class="flex-1 relative">
                    <textarea x-model="newMessage" 
                              @keydown.enter.prevent="sendMessage()"
                              @input="handleTyping"
                              placeholder="Type your message..." 
                              rows="1"
                              class="w-full border border-gray-300 rounded-2xl px-4 py-3 pr-12 resize-none focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200 chat-input"
                              :disabled="isLoading"
                              style="min-height: 44px; max-height: 120px;"></textarea>
                </div>

                <!-- Send Button -->
                <button type="submit" 
                        :disabled="(!newMessage.trim() && !selectedFile) || isLoading"
                        class="flex-shrink-0 bg-blue-600 text-white p-3 rounded-full hover:bg-blue-700 disabled:opacity-50 disabled:cursor-not-allowed transition-all duration-200 hover:scale-105 focus:outline-none focus:ring-4 focus:ring-blue-300">
                    <svg x-show="!isLoading" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path>
                    </svg>
                    <svg x-show="isLoading" class="w-5 h-5 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                    </svg>
                </button>
            </form>
        </div>
    </div>
</div>

<script>
function unifiedChatWidget() {
    return {
        isOpen: false,
        isLoading: false,
        isTyping: false,
        newMessage: '',
        messages: [],
        chatId: null,
        isGuest: true,
        unreadCount: 0,
        selectedFile: null,
        typingTimeout: null,
        guestInfo: {
            name: '',
            email: ''
        },

        init() {
            // Check if user is authenticated
            this.isGuest = !window.authUser;
            
            if (this.isGuest) {
                this.showGuestForm();
            } else {
                this.loadUserChats();
            }
            
            // Start polling for new messages
            this.startPolling();
        },

        toggleChat() {
            this.isOpen = !this.isOpen;
            if (this.isOpen) {
                // Clear unread count when chat is opened
                this.unreadCount = 0;
                if (this.messages.length === 0) {
                    this.startChat();
                }
            }
        },

        async startChat() {
            if (this.isGuest) {
                if (!this.guestInfo.name || !this.guestInfo.email) {
                    this.showGuestForm();
                    return;
                }
                
                try {
                    this.isLoading = true;
                    const response = await fetch('/api/chat/start', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        },
                        body: JSON.stringify({
                            name: this.guestInfo.name,
                            email: this.guestInfo.email,
                            message: this.newMessage || 'Hello, I need help',
                            topic: 'general'
                        })
                    });

                    const data = await response.json();
                    if (data.success) {
                        this.chatId = data.chat_id;
                        // Add the initial message to the UI
                        this.messages.push({
                            id: Date.now(),
                            message: this.newMessage || 'Hello, I need help',
                            sender_id: null,
                            created_at: new Date().toISOString()
                        });
                        this.newMessage = ''; // Clear the input
                        this.scrollToBottom();
                    } else {
                        console.error('Failed to start chat:', data);
                    }
                } catch (error) {
                    console.error('Error starting chat:', error);
                } finally {
                    this.isLoading = false;
                }
            } else {
                await this.loadUserChats();
            }
        },

        async sendMessage() {
            if ((!this.newMessage.trim() && !this.selectedFile) || this.isLoading) return;

            const message = this.newMessage;
            this.newMessage = '';
            const file = this.selectedFile;
            this.selectedFile = null;

            // Ensure we have a chat ID before sending message
            if (!this.chatId) {
                if (this.isGuest) {
                    // For guests, start a new chat first
                    await this.startChat();
                    if (!this.chatId) {
                        console.error('Failed to create chat');
                        return;
                    }
                } else {
                    // For authenticated users, load existing chats or create new one
                    await this.loadUserChats();
                    if (!this.chatId) {
                        console.error('No chat available');
                        return;
                    }
                }
            }

            // Add message to UI immediately
            this.messages.push({
                id: Date.now(),
                message: message || (file ? `📎 ${file.name}` : ''),
                sender_id: this.isGuest ? null : window.authUser?.id,
                created_at: new Date().toISOString(),
                file_name: file ? file.name : null
            });
            this.updateUnreadCount();
            this.scrollToBottom();

            try {
                this.isLoading = true;
                
                // Prepare form data for file upload
                const formData = new FormData();
                formData.append('message', message || '');
                if (file) {
                    formData.append('file', file);
                }
                if (this.isGuest) {
                    formData.append('sender_email', this.guestInfo.email);
                    formData.append('sender_name', this.guestInfo.name);
                }

                const response = await fetch(`/api/chat/${this.chatId}/message`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: formData
                });

                const data = await response.json();
                if (data.success) {
                    // Update message with server response
                    const lastMessage = this.messages[this.messages.length - 1];
                    lastMessage.id = data.message.id;
                    lastMessage.file_path = data.message.file_path;
                } else {
                    console.error('Failed to send message:', data);
                }
            } catch (error) {
                console.error('Error sending message:', error);
            } finally {
                this.isLoading = false;
            }
        },

        async loadUserChats() {
            try {
                const response = await fetch('/api/chat/user/chats');
                const data = await response.json();
                if (data.success && data.chats.length > 0) {
                    this.chatId = data.chats[0].id;
                    await this.loadMessages();
                } else {
                    // No existing chats, create a new one for authenticated users
                    await this.createUserChat();
                }
            } catch (error) {
                console.error('Error loading chats:', error);
                // Try to create a new chat as fallback
                await this.createUserChat();
            }
        },

        async createUserChat() {
            try {
                this.isLoading = true;
                const response = await fetch('/api/chat/start', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({
                        name: window.authUser?.name || 'User',
                        email: window.authUser?.email || 'user@example.com',
                        message: this.newMessage || 'Hello, I need help',
                        topic: 'general'
                    })
                });

                const data = await response.json();
                if (data.success) {
                    this.chatId = data.chat_id;
                    // Add the initial message to the UI
                    this.messages.push({
                        id: Date.now(),
                        message: this.newMessage || 'Hello, I need help',
                        sender_id: window.authUser?.id,
                        created_at: new Date().toISOString()
                    });
                    this.newMessage = ''; // Clear the input
                    this.scrollToBottom();
                } else {
                    console.error('Failed to create chat:', data);
                }
            } catch (error) {
                console.error('Error creating chat:', error);
            } finally {
                this.isLoading = false;
            }
        },

        async loadMessages() {
            if (!this.chatId) return;

            try {
                const response = await fetch(`/api/chat/${this.chatId}/messages`);
                const data = await response.json();
                if (data.success) {
                    this.messages = data.messages;
                    this.scrollToBottom();
                }
            } catch (error) {
                console.error('Error loading messages:', error);
            }
        },

        showGuestForm() {
            const name = prompt('Please enter your name:');
            const email = prompt('Please enter your email:');
            
            if (name && email) {
                this.guestInfo = { name, email };
            }
        },

        formatTime(timestamp) {
            const date = new Date(timestamp);
            const now = new Date();
            const diffInMinutes = Math.floor((now - date) / (1000 * 60));
            
            if (diffInMinutes < 1) {
                return 'Just now';
            } else if (diffInMinutes < 60) {
                return `${diffInMinutes}m ago`;
            } else if (diffInMinutes < 1440) {
                return `${Math.floor(diffInMinutes / 60)}h ago`;
            } else {
                return date.toLocaleDateString([], { month: 'short', day: 'numeric' });
            }
        },

        isUserMessage(message) {
            // Check if message is from the current user
            if (this.isGuest) {
                return !message.sender_id; // Guest messages have no sender_id
            } else {
                return message.sender_id === (window.authUser?.id || null);
            }
        },

        handleFileSelect(event) {
            const file = event.target.files[0];
            if (file) {
                this.selectedFile = file;
            }
        },

        handleTyping() {
            // Show typing indicator for user
            this.isTyping = true;
            
            // Auto-resize textarea
            this.autoResizeTextarea();
            
            // Clear existing timeout
            if (this.typingTimeout) {
                clearTimeout(this.typingTimeout);
            }
            
            // Hide typing indicator after 3 seconds of inactivity
            this.typingTimeout = setTimeout(() => {
                this.isTyping = false;
            }, 3000);
        },

        autoResizeTextarea() {
            this.$nextTick(() => {
                const textarea = document.querySelector('textarea[x-model="newMessage"]');
                if (textarea) {
                    textarea.style.height = 'auto';
                    textarea.style.height = Math.min(textarea.scrollHeight, 120) + 'px';
                }
            });
        },

        updateUnreadCount() {
            // Count unread messages (messages not from current user)
            this.unreadCount = this.messages.filter(message => !this.isUserMessage(message)).length;
        },

        scrollToBottom() {
            this.$nextTick(() => {
                const chatMessages = document.getElementById('chatMessages');
                chatMessages.scrollTop = chatMessages.scrollHeight;
            });
        },

        // Real-time methods with enhanced polling
        startPolling() {
            // Enhanced polling for real-time updates
            this.pollInterval = setInterval(() => {
                if (this.chatId && this.isOpen) {
                    this.pollForNewMessages();
                }
            }, 3000); // Poll every 3 seconds for real-time feel
            
            // Also poll when chat is closed but user is on the page
            this.backgroundPollInterval = setInterval(() => {
                if (this.chatId && !this.isOpen) {
                    this.pollForNewMessages();
                }
            }, 5000); // Poll every 5 seconds in background
        },

        initializeRealtimeConnection() {
            // Check if Pusher is available (if using Pusher driver)
            if (typeof window.Pusher !== 'undefined') {
                this.initializePusher();
            } else if (typeof window.Echo !== 'undefined') {
                this.initializeEcho();
            } else {
                console.log('Real-time broadcasting not available, using polling fallback');
                this.initializePollingBroadcast();
            }
        },

        initializePollingBroadcast() {
            // Use polling-based broadcasting
            this.broadcastPollingInterval = setInterval(() => {
                if (this.chatId) {
                    this.pollForBroadcastEvents();
                }
            }, 3000); // Poll every 3 seconds for broadcast events
        },

        async pollForBroadcastEvents() {
            if (!this.chatId) return;

            try {
                const response = await fetch(`/api/chat/events/chat.${this.chatId}?since=${this.lastEventTimestamp || 0}`);
                const data = await response.json();
                
                if (data.success && data.events.length > 0) {
                    data.events.forEach(event => {
                        if (event.event === 'new-message') {
                            this.handleNewMessage(event.data.message);
                        }
                    });
                    this.lastEventTimestamp = data.timestamp;
                }
            } catch (error) {
                console.error('Error polling for broadcast events:', error);
            }
        },

        initializePusher() {
            try {
                this.pusher = new window.Pusher(window.Laravel.pusherKey, {
                    cluster: window.Laravel.pusherCluster,
                    encrypted: true,
                    authEndpoint: '/broadcasting/auth',
                    auth: {
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        }
                    }
                });

                this.pusher.connection.bind('connected', () => {
                    console.log('Pusher connected');
                    this.subscribeToChatChannel();
                });

                this.pusher.connection.bind('disconnected', () => {
                    console.log('Pusher disconnected, falling back to polling');
                });
            } catch (error) {
                console.error('Pusher initialization failed:', error);
            }
        },

        initializeEcho() {
            try {
                this.echo = window.Echo.private(`chat.${this.chatId}`)
                    .listen('new-message', (e) => {
                        this.handleNewMessage(e.message);
                    });
                console.log('Echo connected to chat channel');
            } catch (error) {
                console.error('Echo initialization failed:', error);
            }
        },

        subscribeToChatChannel() {
            if (!this.chatId || !this.pusher) return;

            try {
                this.channel = this.pusher.subscribe(`private-chat.${this.chatId}`);
                
                this.channel.bind('new-message', (data) => {
                    this.handleNewMessage(data.message);
                });

                console.log('Subscribed to chat channel:', this.chatId);
            } catch (error) {
                console.error('Failed to subscribe to chat channel:', error);
            }
        },

        handleNewMessage(messageData) {
            // Check if message already exists to avoid duplicates
            const exists = this.messages.some(msg => msg.id === messageData.id);
            if (!exists) {
                this.messages.push({
                    id: messageData.id,
                    message: messageData.message,
                    sender_id: messageData.sender_id,
                    sender_name: messageData.sender_name,
                    created_at: messageData.created_at,
                    message_type: messageData.message_type,
                    file_path: messageData.file_path,
                    file_name: messageData.file_name
                });
                
                this.updateUnreadCount();
                this.scrollToBottom();
                
                // Show notification if chat is not open and message is from admin
                if (!this.isOpen && messageData.sender_id && messageData.sender_id !== (window.authUser?.id || null)) {
                    this.showNotification('New message from ' + (messageData.sender_name || 'Admin'));
                }
            }
        },

        showNotification(message) {
            // Simple notification - can be enhanced with a proper notification system
            if ('Notification' in window && Notification.permission === 'granted') {
                new Notification('New Chat Message', {
                    body: message,
                    icon: '/favicon.ico'
                });
            } else {
                // Fallback to console or visual indicator
                console.log('Notification:', message);
            }
        },

        stopPolling() {
            if (this.pollInterval) {
                clearInterval(this.pollInterval);
                this.pollInterval = null;
            }
            
            if (this.backgroundPollInterval) {
                clearInterval(this.backgroundPollInterval);
                this.backgroundPollInterval = null;
            }
            
            if (this.broadcastPollingInterval) {
                clearInterval(this.broadcastPollingInterval);
                this.broadcastPollingInterval = null;
            }
            
            // Clean up real-time connections
            if (this.pusher) {
                this.pusher.disconnect();
                this.pusher = null;
            }
            
            if (this.echo) {
                this.echo.leave(`chat.${this.chatId}`);
                this.echo = null;
            }
        },

        async pollForNewMessages() {
            if (!this.chatId) return;

            try {
                const response = await fetch(`/api/chat/${this.chatId}/messages?since=${this.getLastMessageTime()}`);
                const data = await response.json();
                
                if (data.success && data.messages.length > 0) {
                    let hasNewMessages = false;
                    
                    // Add new messages to the UI
                    data.messages.forEach(newMessage => {
                        // Check if message already exists to avoid duplicates
                        const exists = this.messages.some(msg => msg.id === newMessage.id);
                        if (!exists) {
                            this.messages.push(newMessage);
                            hasNewMessages = true;
                            
                            // Show notification if chat is closed and message is from admin
                            if (!this.isOpen && newMessage.sender_id && newMessage.sender_id !== (window.authUser?.id || null)) {
                                this.showNotification('New message from ' + (newMessage.sender_name || 'Admin'));
                            }
                        }
                    });
                    
                    if (hasNewMessages) {
                        this.updateUnreadCount();
                        this.scrollToBottom();
                    }
                }
            } catch (error) {
                console.error('Error polling for messages:', error);
            }
        },

        getLastMessageTime() {
            if (this.messages.length === 0) {
                return new Date().toISOString();
            }
            
            const lastMessage = this.messages[this.messages.length - 1];
            return lastMessage.created_at || new Date().toISOString();
        },

        // Cleanup when component is destroyed
        destroy() {
            this.stopPolling();
        }
    }
}
</script>

<style>
/* Chrome-optimized chat widget styles */
.chat-widget {
    font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
}

/* Custom scrollbar for Chrome */
#chatMessages::-webkit-scrollbar {
    width: 6px;
}

#chatMessages::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 3px;
}

#chatMessages::-webkit-scrollbar-thumb {
    background: #c1c1c1;
    border-radius: 3px;
}

#chatMessages::-webkit-scrollbar-thumb:hover {
    background: #a8a8a8;
}

/* Smooth animations */
.chat-message-enter {
    opacity: 0;
    transform: translateY(10px);
}

.chat-message-enter-active {
    opacity: 1;
    transform: translateY(0);
    transition: opacity 0.3s ease, transform 0.3s ease;
}

/* Typing indicator animation */
.typing-dot {
    animation: typing 1.4s infinite ease-in-out;
}

.typing-dot:nth-child(1) {
    animation-delay: -0.32s;
}

.typing-dot:nth-child(2) {
    animation-delay: -0.16s;
}

@keyframes typing {
    0%, 80%, 100% {
        transform: scale(0);
        opacity: 0.5;
    }
    40% {
        transform: scale(1);
        opacity: 1;
    }
}

/* Message bubble shadows */
.message-bubble {
    box-shadow: 0 1px 2px rgba(0, 0, 0, 0.1);
}

.message-bubble:hover {
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.15);
}

/* Focus states for accessibility */
.chat-input:focus {
    box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
}

/* Unread badge animation */
.unread-badge {
    animation: pulse 2s infinite;
}

@keyframes pulse {
    0%, 100% {
        transform: scale(1);
    }
    50% {
        transform: scale(1.1);
    }
}

/* File upload preview */
.file-preview {
    background: linear-gradient(135deg, #dbeafe 0%, #bfdbfe 100%);
    border: 1px solid #93c5fd;
}

/* Responsive design for mobile */
@media (max-width: 640px) {
    .chat-window {
        width: calc(100vw - 2rem);
        right: 1rem;
        left: 1rem;
    }
}
</style>
