@extends('layouts.admin')

@section('content')
<div class="h-screen flex bg-gray-50" x-data="adminChatPanel()">
    <!-- Side Panel for Conversations -->
    <div class="w-80 bg-white shadow-lg flex flex-col">
        <!-- Header -->
        <div class="p-4 border-b border-gray-200 bg-white">
            <div class="flex items-center justify-between mb-4">
                <h1 class="text-xl font-bold text-gray-900">Chat Management</h1>
                <button @click="refreshData()" class="text-gray-500 hover:text-gray-700" title="Refresh">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                    </svg>
                        </button>
                    </div>

            <!-- Statistics -->
            <div class="grid grid-cols-2 gap-3 mb-4">
                <div class="bg-blue-50 p-3 rounded-lg">
                    <div class="text-xs text-blue-600 font-medium">Total Messages</div>
                    <div class="text-lg font-bold text-blue-900" x-text="stats.total_messages || 0">0</div>
                </div>
                <div class="bg-green-50 p-3 rounded-lg">
                    <div class="text-xs text-green-600 font-medium">Active Users</div>
                    <div class="text-lg font-bold text-green-900" x-text="stats.total_users_with_chats || 0">0</div>
                            </div>
                <div class="bg-yellow-50 p-3 rounded-lg">
                    <div class="text-xs text-yellow-600 font-medium">Unread</div>
                    <div class="text-lg font-bold text-yellow-900" x-text="stats.unread_messages || 0">0</div>
                            </div>
                <div class="bg-purple-50 p-3 rounded-lg">
                    <div class="text-xs text-purple-600 font-medium">Today</div>
                    <div class="text-lg font-bold text-purple-900" x-text="stats.messages_today || 0">0</div>
                        </div>
                    </div>

            <!-- Search -->
            <div class="mb-4">
                <input type="text"
                       placeholder="Search conversations..."
                       class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                       x-model="searchQuery"
                       @input="filterConversations()">
                            </div>

            <!-- Quick Actions -->
            <div class="mb-4">
                <h3 class="text-sm font-semibold text-gray-700 mb-3">Quick Actions</h3>
                <button @click="refreshData()" class="w-full px-3 py-2 bg-blue-500 text-white text-sm rounded-md hover:bg-blue-600 transition-colors">
                    <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                    </svg>
                    Refresh Conversations
                </button>
            </div>
                            </div>

        <!-- Conversations List -->
        <div class="flex-1 overflow-y-auto">
            <!-- Loading State -->
            <div x-show="conversations.length === 0 && !loading" class="text-center text-gray-500 py-8 px-4">
                <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-500 mx-auto mb-4"></div>
                <p>Loading conversations...</p>
            </div>
            
            <!-- No Conversations State -->
            <div x-show="filteredConversations.length === 0 && conversations.length > 0" class="text-center text-gray-500 py-8 px-4">
                <p>No conversations match your search</p>
                <p class="text-sm mt-1">Try adjusting your search terms</p>
            </div>
            
            <!-- Empty State -->
            <div x-show="conversations.length === 0 && !loading" class="text-center text-gray-500 py-8 px-4">
                <div class="w-16 h-16 bg-gray-200 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                    </svg>
                </div>
                <p>No conversations found</p>
                <p class="text-sm mt-1">Start chatting with users to see them here</p>
            </div>

            <!-- Conversations List -->
            <template x-for="conversation in filteredConversations" :key="conversation.id">
                <div class="p-4 border-b border-gray-100 hover:bg-gray-50 cursor-pointer transition-colors group"
                     :class="{ 'bg-blue-50 border-blue-200': selectedConversation && selectedConversation.id === conversation.id }"
                     @click="openConversation(conversation)">
                    <div class="flex items-start justify-between">
                        <div class="flex items-center flex-1 min-w-0">
                            <!-- Avatar -->
                            <div class="w-10 h-10 bg-gradient-to-br from-blue-500 to-blue-600 rounded-full flex items-center justify-center text-white font-semibold text-sm mr-3 flex-shrink-0 shadow-sm">
                                <span x-text="(conversation.user_name || 'U').charAt(0).toUpperCase()"></span>
                            </div>

                            <!-- User Info -->
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center justify-between mb-1">
                                    <div class="flex items-center min-w-0">
                                        <p class="text-sm font-semibold text-gray-900 truncate" x-text="conversation.user_name || 'Unknown User'"></p>
                                        <span x-show="conversation.is_guest" class="ml-2 px-2 py-0.5 text-xs bg-yellow-100 text-yellow-800 rounded-full font-medium">Guest</span>
                                    </div>
                                    <div class="flex items-center space-x-2 ml-2">
                                        <!-- Unread Badge -->
                                        <div x-show="conversation.unread_count > 0"
                                             class="bg-red-500 text-white text-xs rounded-full h-5 w-5 flex items-center justify-center font-medium">
                                            <span x-text="conversation.unread_count"></span>
                                        </div>
                                        <!-- Online Status -->
                                        <div class="w-2 h-2 rounded-full flex-shrink-0"
                                             :class="conversation.is_online ? 'bg-green-500' : 'bg-gray-400'">
                                        </div>
                                    </div>
                                </div>
                                <p class="text-xs text-gray-500 truncate mb-1" x-text="conversation.user_email || 'No email'"></p>
                                <div class="flex items-center justify-between">
                                    <p class="text-xs text-gray-600 truncate flex-1 mr-2" x-text="conversation.last_message || 'No messages yet'"></p>
                                    <span class="text-xs text-gray-400 flex-shrink-0" x-text="formatTime(conversation.last_message_at)"></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </template>
        </div>
                    </div>

    <!-- Main Chat Area -->
    <div class="flex-1 flex flex-col" x-show="selectedConversation">
        <!-- Chat Header -->
        <div class="bg-white border-b border-gray-200 p-4 flex items-center justify-between shadow-sm">
            <div class="flex items-center">
                <div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-blue-600 rounded-full flex items-center justify-center text-white font-semibold mr-4 shadow-sm">
                    <span x-text="(selectedConversation.user_name || 'U').charAt(0).toUpperCase()"></span>
                </div>
                <div>
                    <div class="flex items-center">
                        <h2 class="text-lg font-semibold text-gray-900" x-text="selectedConversation.user_name || 'Unknown User'"></h2>
                        <span x-show="selectedConversation.is_guest" class="ml-2 px-2 py-1 text-xs bg-yellow-100 text-yellow-800 rounded-full font-medium">Guest</span>
                    </div>
                    <p class="text-sm text-gray-500" x-text="selectedConversation.user_email || 'No email'"></p>
                </div>
            </div>
            <div class="flex items-center space-x-3">
                <span class="px-3 py-1 text-xs rounded-full font-medium"
                      :class="selectedConversation.is_online ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800'">
                    <span x-text="selectedConversation.is_online ? 'Online' : 'Offline'"></span>
                </span>
                <button @click="closeConversation()" class="text-gray-500 hover:text-gray-700 p-1 rounded-full hover:bg-gray-100 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
        </div>
                                    
        <!-- Messages Area -->
        <div class="flex-1 overflow-y-auto p-6 bg-gray-50" id="chat-messages-area">
            <!-- Loading State -->
            <div x-show="loading" class="text-center text-gray-500 py-12">
                <div class="animate-spin rounded-full h-10 w-10 border-b-2 border-blue-500 mx-auto mb-4"></div>
                <p class="text-sm">Loading messages...</p>
            </div>
                                        
            <!-- Empty State -->
            <div x-show="!loading && messages.length === 0" class="text-center text-gray-500 py-12">
                <div class="w-16 h-16 bg-gray-200 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                    </svg>
                </div>
                <p class="text-lg font-medium mb-2">No messages yet</p>
                <p class="text-sm">Start the conversation by sending a message!</p>
            </div>
                                        
            <!-- Messages -->
            <template x-for="message in messages" :key="message.id">
                <div class="mb-6" :class="{ 'flex justify-end': message.sender_id === currentUserId, 'flex justify-start': message.sender_id !== currentUserId }">
                    <div class="max-w-xs lg:max-w-md px-4 py-3 rounded-2xl shadow-sm"
                         :class="{ 
                             'bg-blue-500 text-white rounded-br-md': message.sender_id === currentUserId,
                             'bg-white text-gray-900 border border-gray-200 rounded-bl-md': message.sender_id !== currentUserId
                         }">
                        <div class="text-xs opacity-75 mb-2 font-medium"
                             x-text="message.sender_id === currentUserId ? 'You' : (message.sender ? message.sender.name : (selectedConversation ? selectedConversation.user_name : 'User'))">
                        </div>
                        <div class="text-sm leading-relaxed" x-text="message.message"></div>
                        <div class="text-xs opacity-75 mt-2"
                             x-text="formatTime(message.created_at)">
                        </div>
                    </div>
                </div>
            </template>
        </div>

        <!-- Message Input -->
        <div class="bg-white border-t border-gray-200 p-4 shadow-sm" x-show="selectedConversation">
            <form @submit.prevent="sendMessage()" class="flex items-end space-x-3">
                <div class="flex-1">
                    <div class="relative">
                        <input type="text"
                               x-model="newMessage"
                               placeholder="Type your message... (Press Enter to send)"
                               class="w-full px-4 py-3 pr-12 border border-gray-300 rounded-2xl focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent resize-none"
                               :disabled="sending"
                               @keydown.enter.prevent="sendMessage()">
                        <div class="absolute right-3 top-1/2 transform -translate-y-1/2">
                            <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                            </svg>
                        </div>
                    </div>
                </div>
                <button type="submit"
                        :disabled="!newMessage.trim() || sending"
                        class="px-6 py-3 bg-blue-500 text-white rounded-2xl hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-500 disabled:opacity-50 disabled:cursor-not-allowed transition-colors flex items-center space-x-2">
                    <span x-show="!sending" class="font-medium">Send</span>
                    <span x-show="sending" class="flex items-center">
                        <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        Sending...
                    </span>
                </button>
            </form>
        </div>
    </div>

    <!-- No Conversation Selected -->
    <div class="flex-1 flex items-center justify-center bg-gradient-to-br from-gray-50 to-gray-100" x-show="!selectedConversation">
        <div class="text-center max-w-md mx-auto px-6">
            <div class="w-32 h-32 bg-gradient-to-br from-blue-100 to-blue-200 rounded-full flex items-center justify-center mx-auto mb-6 shadow-lg">
                <svg class="w-16 h-16 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                </svg>
            </div>
            <h3 class="text-2xl font-semibold text-gray-900 mb-3">Welcome to Admin Chat</h3>
            <p class="text-gray-600 mb-6 leading-relaxed">Select a conversation from the sidebar to start chatting with users. You can view all active conversations and respond to customer inquiries in real-time.</p>
            <div class="flex justify-center">
                <button @click="refreshData()" class="px-6 py-3 bg-blue-500 text-white rounded-lg hover:bg-blue-600 transition-colors font-medium">
                    <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                    </svg>
                    Refresh Conversations
                </button>
            </div>
        </div>
    </div>
</div>


<!-- Data Initialization -->
<script>
window.chatData = {
    conversations: @json($conversations ?? []),
    stats: @json($stats ?? []),
    currentUserId: {{ auth()->id() }}
};
</script>

<!-- Alpine.js Logic -->
<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('adminChatPanel', () => ({
        conversations: window.chatData.conversations || [],
        filteredConversations: window.chatData.conversations || [],
        selectedConversation: null,
        messages: [],
        stats: window.chatData.stats || {},
        searchQuery: '',
        newMessage: '',
        sending: false,
        loading: false,
        currentUserId: {{ auth()->id() }},
        pollingInterval: null,
        
        init() {
            console.log('AdminChatPanel initialized');
            
            // Make this instance globally accessible for debugging
            window.adminChatPanel = this;
            
            // Check if conversations are loaded
            if (this.conversations.length === 0) {
                console.log('No conversations loaded initially, refreshing...');
                this.refreshData();
            } else {
                console.log('Loaded conversations:', this.conversations.length);
            }
            
            this.loadStats();
            this.startPolling();
            
            // Auto-refresh conversations every 30 seconds
            setInterval(() => {
                this.refreshData();
            }, 30000);
        },
        
        async loadStats() {
            try {
                const response = await fetch('/admin/chat/statistics');
                const data = await response.json();
                
                if (data.success) {
                    this.stats = data.stats;
                }
            } catch (error) {
                console.error('Error loading stats:', error);
            }
        },
        
        filterConversations() {
            if (!this.searchQuery.trim()) {
                this.filteredConversations = this.conversations;
                return;
            }
            
            this.filteredConversations = this.conversations.filter(conv =>
                conv.user_name.toLowerCase().includes(this.searchQuery.toLowerCase()) ||
                conv.user_email.toLowerCase().includes(this.searchQuery.toLowerCase())
            );
        },

        openConversation(conversation) {
            console.log('Opening conversation:', conversation);
            this.selectedConversation = conversation;
            console.log('Selected conversation set to:', this.selectedConversation);
            this.loadMessages(conversation.id);
        },

        closeConversation() {
            this.selectedConversation = null;
            this.messages = [];
        },

        async loadMessages(userId) {
            this.loading = true;
            console.log('Starting loadMessages for userId:', userId);
            console.log('Selected conversation:', this.selectedConversation);
            
            try {
                // Handle guest users differently
                let url;
                if (userId.startsWith('guest_')) {
                    // For guest users, use the chat ID directly
                    const chatId = userId.replace('guest_', '');
                    url = `/admin/chat/unified/${chatId}/messages`;
                } else {
                    // For registered users, use the user ID
                    url = `/admin/chat/${userId}/messages`;
                }
                
                console.log('Fetching from URL:', url);
                
                const response = await fetch(url, {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                });
                
                console.log('Response status:', response.status);
                
                if (!response.ok) {
                    const errorText = await response.text();
                    console.error('Response error text:', errorText);
                    throw new Error(`HTTP error! status: ${response.status} - ${errorText}`);
                }
                
                const data = await response.json();
                console.log('Messages response data:', data);

                if (data.success) {
                    this.messages = data.messages || [];
                    console.log('Messages set to:', this.messages);
                    this.scrollToBottom();
                    this.markConversationAsRead(userId);
                } else {
                    console.error('API returned success: false', data);
                    this.messages = [];
                }
            } catch (error) {
                console.error('Error in loadMessages:', error);
                this.messages = []; // Reset to empty array on error
            } finally {
                this.loading = false;
                console.log('loadMessages completed, loading:', this.loading);
            }
        },

        markConversationAsRead(userId) {
            // Update the conversation's unread count
            const conversation = this.conversations.find(c => c.id === userId);
            if (conversation) {
                conversation.unread_count = 0;
            }
        },

        async sendMessage() {
            console.log('sendMessage called');
            console.log('newMessage:', this.newMessage);
            console.log('selectedConversation:', this.selectedConversation);
            
            if (!this.newMessage.trim() || !this.selectedConversation) {
                console.log('Cannot send message - validation failed:', {
                    hasMessage: !!this.newMessage.trim(),
                    hasConversation: !!this.selectedConversation,
                    messageLength: this.newMessage.length
                });
                return;
            }

            console.log('Sending message to user:', this.selectedConversation.id);
            this.sending = true;

            try {
                const formData = new FormData();
                formData.append('message', this.newMessage);
                
                // Handle guest users differently
                let url;
                if (this.selectedConversation.id.startsWith('guest_')) {
                    // For guest users, use the unified chat endpoint
                    const chatId = this.selectedConversation.id.replace('guest_', '');
                    url = `/admin/chat/unified/${chatId}/reply`;
                } else {
                    // For registered users, use the regular chat endpoint
                    url = `/admin/chat/${this.selectedConversation.id}/reply`;
                }
                
                console.log('Sending to URL:', url);

                const response = await fetch(url, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: formData
                });

                console.log('Send message response status:', response.status);

                if (!response.ok) {
                    const errorText = await response.text();
                    console.error('Send message error response:', errorText);
                    throw new Error(`HTTP error! status: ${response.status} - ${errorText}`);
                }

                const data = await response.json();
                console.log('Message sent response data:', data);
                
                if (data.success && data.message) {
                    console.log('Adding message to messages array:', data.message);
                    this.messages.push(data.message);
                    this.newMessage = '';
                    this.scrollToBottom();
                    this.refreshData();
                } else {
                    console.error('Send message API returned success: false', data);
                    alert('Failed to send message. Please try again.');
                }
            } catch (error) {
                console.error('Error in sendMessage:', error);
                alert('Error sending message. Please try again.');
            } finally {
                this.sending = false;
                console.log('sendMessage completed, sending:', this.sending);
            }
        },

        startPolling() {
            this.pollingInterval = setInterval(() => {
                this.checkForNewMessages();
                this.loadStats();
            }, 5000);
        },

        stopPolling() {
            if (this.pollingInterval) {
                clearInterval(this.pollingInterval);
            }
        },

        async checkForNewMessages() {
            if (!this.selectedConversation) return;

            try {
                const lastMessageId = this.messages.length > 0 ? this.messages[this.messages.length - 1].id : 0;
                const response = await fetch(`/admin/chat/${this.selectedConversation.id}/messages?since=${lastMessageId}`);
                if (response.ok) {
                    const data = await response.json();
                    if (data.success && data.messages && data.messages.length > 0) {
                        this.messages.push(...data.messages);
                        this.scrollToBottom();
                        this.refreshData();
                    }
                }
            } catch (error) {
                console.error('Error checking for new messages:', error);
            }
        },

        async refreshData() {
            try {
                const response = await fetch('/admin/chat', {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                });
                
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                
                const data = await response.json();

                if (data.success) {
                    this.conversations = data.conversations || [];
                    this.filterConversations();
                    console.log('Conversations updated:', this.conversations.length);
                } else {
                    console.error('API returned success: false', data);
                }
            } catch (error) {
                console.error('Error refreshing data:', error);
            }
        },

        scrollToBottom() {
            this.$nextTick(() => {
                const container = document.getElementById('chat-messages-area');
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
        

        destroy() {
            this.stopPolling();
        }
    }));
});
</script>
@endsection

