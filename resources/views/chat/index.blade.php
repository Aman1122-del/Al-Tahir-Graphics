@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gray-100">
    <div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6">
                <h1 class="text-2xl font-bold text-gray-900 mb-6">Chat</h1>
                
                <div class="flex h-96">
                    <!-- User List Sidebar -->
                    <div class="w-1/3 border-r border-gray-200 bg-gray-50">
                        <div class="p-4">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Conversations</h3>
                            
                            <!-- Search -->
                            <div class="mb-4">
                                <input type="text" 
                                       placeholder="Search users..." 
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                                       x-model="searchQuery"
                                       @input="filterUsers()">
                            </div>
                            
                            <!-- User List -->
                            <div class="space-y-2 max-h-80 overflow-y-auto">
                                <template x-for="user in filteredUsers" :key="user.id">
                                    <div class="flex items-center p-3 rounded-lg hover:bg-gray-100 cursor-pointer transition-colors"
                                         :class="{ 'bg-blue-100 border-l-4 border-blue-500': selectedUserId === user.id }"
                                         @click="selectUser(user)">
                                        
                                        <!-- Avatar -->
                                        <div class="w-10 h-10 bg-blue-500 rounded-full flex items-center justify-center text-white font-semibold mr-3">
                                            <span x-text="user.name.charAt(0).toUpperCase()"></span>
                                        </div>
                                        
                                        <!-- User Info -->
                                        <div class="flex-1 min-w-0">
                                            <p class="text-sm font-medium text-gray-900 truncate" x-text="user.name"></p>
                                            <p class="text-xs text-gray-500 truncate" x-text="user.email"></p>
                                        </div>
                                        
                                        <!-- Unread Badge -->
                                        <div x-show="user.unread_count > 0" 
                                             class="ml-2 bg-red-500 text-white text-xs rounded-full h-5 w-5 flex items-center justify-center">
                                            <span x-text="user.unread_count"></span>
                                        </div>
                                    </div>
                                </template>
                                
                                <div x-show="filteredUsers.length === 0" class="text-center text-gray-500 py-4">
                                    No users found
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Chat Area -->
                    <div class="flex-1 flex flex-col">
                        <!-- Chat Header -->
                        <div class="border-b border-gray-200 p-4 bg-white">
                            <div x-show="selectedUser" class="flex items-center">
                                <div class="w-8 h-8 bg-blue-500 rounded-full flex items-center justify-center text-white font-semibold mr-3">
                                    <span x-text="selectedUser.name.charAt(0).toUpperCase()"></span>
                                </div>
                                <div>
                                    <h3 class="text-lg font-medium text-gray-900" x-text="selectedUser.name"></h3>
                                    <p class="text-sm text-gray-500" x-text="selectedUser.email"></p>
                                </div>
                            </div>
                            <div x-show="!selectedUser" class="text-center text-gray-500">
                                Select a user to start chatting
                            </div>
                        </div>
                        
                        <!-- Messages Area -->
                        <div class="flex-1 p-4 overflow-y-auto bg-gray-50" id="messages-container">
                            <template x-for="message in messages" :key="message.id">
                                <div class="mb-4" :class="{ 'text-right': message.sender_id === currentUserId }">
                                    <div class="inline-block max-w-xs lg:max-w-md px-4 py-2 rounded-lg"
                                         :class="{ 
                                             'bg-blue-500 text-white': message.sender_id === currentUserId,
                                             'bg-white text-gray-900 border border-gray-200': message.sender_id !== currentUserId
                                         }">
                                        
                                        <!-- Message Text -->
                                        <p class="text-sm" x-text="message.message"></p>
                                        
                                        <!-- File Attachment -->
                                        <div x-show="message.file_path" class="mt-2">
                                            <a :href="message.file_url" 
                                               target="_blank"
                                               class="inline-flex items-center text-xs underline"
                                               :class="{ 
                                                   'text-blue-200': message.sender_id === currentUserId,
                                                   'text-blue-600': message.sender_id !== currentUserId
                                               }">
                                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"></path>
                                                </svg>
                                                <span x-text="message.file_name"></span>
                                            </a>
                                        </div>
                                        
                                        <!-- Timestamp -->
                                        <p class="text-xs mt-1 opacity-75" 
                                           x-text="new Date(message.created_at).toLocaleTimeString()"></p>
                                    </div>
                                </div>
                            </template>
                            
                            <div x-show="messages.length === 0 && selectedUser" class="text-center text-gray-500 py-8">
                                No messages yet. Start the conversation!
                            </div>
                        </div>
                        
                        <!-- Message Input -->
                        <div class="border-t border-gray-200 p-4 bg-white" x-show="selectedUser">
                            <form @submit.prevent="sendMessage()" class="flex space-x-3">
                                <!-- File Upload -->
                                <div class="flex-shrink-0">
                                    <label for="file-upload" class="cursor-pointer">
                                        <svg class="w-6 h-6 text-gray-400 hover:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"></path>
                                        </svg>
                                    </label>
                                    <input id="file-upload" type="file" class="hidden" @change="handleFileSelect($event)">
                                </div>
                                
                                <!-- Message Input -->
                                <div class="flex-1">
                                    <input type="text" 
                                           x-model="newMessage" 
                                           placeholder="Type your message..."
                                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                                           :disabled="sending">
                                </div>
                                
                                <!-- Send Button -->
                                <button type="submit" 
                                        class="px-4 py-2 bg-blue-500 text-white rounded-md hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-500 disabled:opacity-50"
                                        :disabled="!newMessage.trim() || sending">
                                    <span x-show="!sending">Send</span>
                                    <span x-show="sending">Sending...</span>
                                </button>
                            </form>
                            
                            <!-- File Preview -->
                            <div x-show="selectedFile" class="mt-3 p-2 bg-gray-100 rounded-md">
                                <div class="flex items-center justify-between">
                                    <span class="text-sm text-gray-600" x-text="selectedFile.name"></span>
                                    <button @click="selectedFile = null" class="text-red-500 hover:text-red-700">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Alpine.js Chat Logic -->
<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('chat', () => ({
        users: @json($chatUsers),
        filteredUsers: @json($chatUsers),
        selectedUser: null,
        selectedUserId: null,
        messages: [],
        newMessage: '',
        selectedFile: null,
        sending: false,
        searchQuery: '',
        currentUserId: {{ auth()->id() }},
        
        init() {
            this.loadUnreadCount();
            this.initializePusher();
        },
        
        filterUsers() {
            if (!this.searchQuery.trim()) {
                this.filteredUsers = this.users;
                return;
            }
            
            this.filteredUsers = this.users.filter(user => 
                user.name.toLowerCase().includes(this.searchQuery.toLowerCase()) ||
                user.email.toLowerCase().includes(this.searchQuery.toLowerCase())
            );
        },
        
        selectUser(user) {
            this.selectedUser = user;
            this.selectedUserId = user.id;
            this.loadMessages(user.id);
            this.markAsRead(user.id);
        },
        
        async loadMessages(userId) {
            try {
                const response = await fetch(`/chat/messages/${userId}`);
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
            
            this.sending = true;
            
            try {
                const formData = new FormData();
                formData.append('receiver_id', this.selectedUserId);
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
        
        async markAsRead(userId) {
            try {
                await fetch('/chat/mark-read', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({ sender_id: userId })
                });
                
                // Update unread count in user list
                const user = this.users.find(u => u.id === userId);
                if (user) {
                    user.unread_count = 0;
                }
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
                    // Update unread count in user list
                    this.users.forEach(user => {
                        const unreadMessages = this.messages.filter(m => 
                            m.sender_id === user.id && 
                            m.receiver_id === this.currentUserId && 
                            !m.read_at
                        );
                        user.unread_count = unreadMessages.length;
                    });
                }
            } catch (error) {
                console.error('Error loading unread count:', error);
            }
        },
        
        scrollToBottom() {
            this.$nextTick(() => {
                const container = document.getElementById('messages-container');
                container.scrollTop = container.scrollHeight;
            });
        },
        
        initializePusher() {
            // Initialize Pusher for real-time updates
            // This will be configured after Pusher is set up
            if (typeof Pusher !== 'undefined') {
                const pusher = new Pusher('{{ config("broadcasting.connections.pusher.key") }}', {
                    cluster: '{{ config("broadcasting.connections.pusher.options.cluster") }}'
                });
                
                const channel = pusher.subscribe('chat-channel');
                channel.bind('new-message', (data) => {
                    if (data.receiver_id === this.currentUserId || data.message.sender_id === this.currentUserId) {
                        // Reload messages if we're in the conversation
                        if (this.selectedUserId === data.message.sender_id || 
                            this.selectedUserId === data.message.receiver_id) {
                            this.loadMessages(this.selectedUserId);
                        }
                        this.loadUnreadCount();
                    }
                });
            }
        }
    }));
});
</script>
@endsection
