@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-blue-50 to-indigo-100">
    <!-- Header with Logo and Branding -->
    <div class="bg-white shadow-sm border-b border-gray-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">
                <div class="flex items-center">
                    <div class="flex-shrink-0 flex items-center">
                        <img src="{{ asset('images/logo.jpg') }}" alt="Al-Tahir Graphics" class="h-8 w-auto">
                        <div class="ml-3">
                            <h1 class="text-xl font-bold text-gray-900">Support Chat</h1>
                            <p class="text-sm text-gray-500">Real-time support assistance</p>
                        </div>
                    </div>
                </div>
                <div class="flex items-center space-x-4">
                    <div class="flex items-center text-sm text-gray-600">
                        <div class="w-2 h-2 bg-green-400 rounded-full animate-pulse mr-2"></div>
                        <span>Online Support</span>
                    </div>
                    <div class="text-sm text-gray-500">{{ auth()->user()->name }}</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Chat Interface -->
    <div class="max-w-6xl mx-auto py-6 px-4 sm:px-6 lg:px-8" x-data="fullPageChat">
        <div class="bg-white rounded-2xl shadow-xl overflow-hidden" style="height: calc(100vh - 200px);">
            <div class="flex h-full">

                <!-- Chat History Sidebar -->
                <div class="w-80 bg-gray-50 border-r border-gray-200 flex flex-col">
                    <!-- Sidebar Header -->
                    <div class="p-4 bg-white border-b border-gray-200">
                        <h2 class="text-lg font-semibold text-gray-900">Chat History</h2>
                        <p class="text-sm text-gray-500">All your conversations</p>
                    </div>

                    <!-- Chat Sessions List -->
                    <div class="flex-1 overflow-y-auto p-4">
                        <div class="space-y-3">
                            <!-- Current Chat Session -->
                            <div class="bg-blue-50 border border-blue-200 rounded-lg p-3 cursor-pointer">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center">
                                        <div class="w-3 h-3 bg-blue-500 rounded-full mr-3"></div>
                                        <div>
                                            <p class="text-sm font-medium text-gray-900">Current Session</p>
                                            <p class="text-xs text-gray-500">Active now</p>
                                        </div>
                                    </div>
                                    <div x-show="unreadCount > 0" class="bg-blue-500 text-white text-xs rounded-full px-2 py-1">
                                        <span x-text="unreadCount"></span>
                                    </div>
                                </div>
                            </div>

                            <!-- Message Count Stats -->
                            <div class="bg-white rounded-lg p-3 border border-gray-200">
                                <div class="text-center">
                                    <div class="text-2xl font-bold text-blue-600" x-text="messages.length"></div>
                                    <div class="text-xs text-gray-500">Total Messages</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Main Chat Area -->
                <div class="flex-1 flex flex-col">
                    <!-- Chat Header -->
                    <div class="bg-white border-b border-gray-200 p-4">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center">
                                <div class="w-10 h-10 bg-gradient-to-r from-blue-500 to-purple-600 rounded-full flex items-center justify-center mr-3">
                                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192L5.636 18.364M12 12h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                </div>
                                <div>
                                    <h3 class="text-lg font-semibold text-gray-900">Al-Tahir Graphics Support</h3>
                                    <p class="text-sm text-gray-500">We're here to help with your design needs</p>
                                </div>
                            </div>
                            <div class="flex items-center space-x-2">
                                <button class="p-2 text-gray-400 hover:text-gray-600 rounded-md hover:bg-gray-100">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                                    </svg>
                                </button>
                                <button class="p-2 text-gray-400 hover:text-gray-600 rounded-md hover:bg-gray-100">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12h.01M12 12h.01M19 12h.01M6 12a1 1 0 11-2 0 1 1 0 012 0zm7 0a1 1 0 11-2 0 1 1 0 012 0zm7 0a1 1 0 11-2 0 1 1 0 012 0z"></path>
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Messages Area -->
                    <div class="flex-1 overflow-y-auto bg-gray-50 p-6" id="full-chat-messages">
                        <!-- Welcome Section -->
                        <div x-show="messages.length === 0" class="text-center py-12">
                            <div class="w-24 h-24 bg-gradient-to-r from-blue-500 to-purple-600 rounded-full flex items-center justify-center mx-auto mb-6">
                                <svg class="w-12 h-12 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                                </svg>
                            </div>
                            <h3 class="text-xl font-semibold text-gray-900 mb-2">Welcome to Al-Tahir Graphics Support</h3>
                            <p class="text-gray-600 max-w-md mx-auto">Start a conversation with our support team. We're here to help with your design projects, questions, and any assistance you need.</p>
                        </div>

                        <!-- Messages -->
                        <div class="space-y-4">
                            <template x-for="message in messages" :key="message.id">
                                <div class="flex" :class="{ 'justify-end': message.sender_id === currentUserId, 'justify-start': message.sender_id !== currentUserId }">
                                    <div class="max-w-xs lg:max-w-md">
                                        <!-- Message Bubble -->
                                        <div class="px-4 py-3 rounded-2xl shadow-sm"
                                             :class="{
                                                 'bg-gradient-to-r from-blue-500 to-blue-600 text-white': message.sender_id === currentUserId,
                                                 'bg-white text-gray-900 border border-gray-200': message.sender_id !== currentUserId
                                             }">

                                            <!-- Sender Name (for received messages) -->
                                            <div x-show="message.sender_id !== currentUserId" class="text-xs text-gray-500 mb-1">
                                                <span x-text="message.sender?.name || 'Support'"></span>
                                            </div>

                                            <!-- Message Text -->
                                            <p class="text-sm leading-relaxed" x-text="message.message"></p>

                                            <!-- File Attachment -->
                                            <div x-show="message.file_path" class="mt-3 p-2 rounded-lg"
                                                 :class="{
                                                     'bg-blue-400 bg-opacity-20': message.sender_id === currentUserId,
                                                     'bg-gray-100': message.sender_id !== currentUserId
                                                 }">
                                                <a :href="message.file_url"
                                                   target="_blank"
                                                   class="inline-flex items-center text-xs font-medium underline"
                                                   :class="{
                                                       'text-blue-100': message.sender_id === currentUserId,
                                                       'text-blue-600': message.sender_id !== currentUserId
                                                   }">
                                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"></path>
                                                    </svg>
                                                    <span x-text="message.file_name"></span>
                                                </a>
                                            </div>
                                        </div>

                                        <!-- Timestamp -->
                                        <div class="mt-1 px-1" :class="{ 'text-right': message.sender_id === currentUserId }">
                                            <span class="text-xs text-gray-500" x-text="formatTime(message.created_at)"></span>
                                        </div>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </div>

                    <!-- Message Input Area -->
                    <div class="bg-white border-t border-gray-200 p-4">
                        <form @submit.prevent="sendMessage()" class="flex items-end space-x-4">
                            <!-- File Upload -->
                            <div class="flex-shrink-0">
                                <label class="cursor-pointer p-3 text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded-full transition-colors">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"></path>
                                    </svg>
                                    <input type="file" class="hidden" @change="handleFileSelect($event)">
                                </label>
                            </div>

                            <!-- Message Input -->
                            <div class="flex-1">
                                <div class="relative">
                                    <textarea x-model="newMessage"
                                              @keydown.enter.prevent="!$event.shiftKey && sendMessage()"
                                              placeholder="Type your message... (Shift+Enter for new line)"
                                              rows="1"
                                              class="w-full px-4 py-3 border border-gray-300 rounded-2xl focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent resize-none"
                                              :disabled="sending"
                                              style="min-height: 50px; max-height: 120px;"></textarea>
                                </div>

                                <!-- File Preview -->
                                <div x-show="selectedFile" class="mt-2 p-3 bg-blue-50 border border-blue-200 rounded-lg">
                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center">
                                            <svg class="w-4 h-4 text-blue-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                            </svg>
                                            <span class="text-sm text-blue-800 font-medium" x-text="selectedFile?.name"></span>
                                        </div>
                                        <button @click="selectedFile = null" class="text-red-500 hover:text-red-700 p-1">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                            </svg>
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <!-- Send Button -->
                            <div class="flex-shrink-0">
                                <button type="submit"
                                        class="p-3 bg-gradient-to-r from-blue-500 to-blue-600 text-white rounded-full hover:from-blue-600 hover:to-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 disabled:opacity-50 disabled:cursor-not-allowed transition-all duration-200 shadow-lg hover:shadow-xl"
                                        :disabled="(!newMessage.trim() && !selectedFile) || sending">
                                    <svg x-show="!sending" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path>
                                    </svg>
                                    <svg x-show="sending" class="w-6 h-6 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                                    </svg>
                                </button>
                            </div>
                        </form>

                        <!-- Typing Indicator -->
                        <div x-show="sending" class="mt-2 text-sm text-gray-500 flex items-center">
                            <div class="flex space-x-1 mr-2">
                                <div class="w-1 h-1 bg-gray-400 rounded-full animate-bounce"></div>
                                <div class="w-1 h-1 bg-gray-400 rounded-full animate-bounce" style="animation-delay: 0.1s"></div>
                                <div class="w-1 h-1 bg-gray-400 rounded-full animate-bounce" style="animation-delay: 0.2s"></div>
                            </div>
                            Sending message...
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Alpine.js Chat Logic (Identical to Floating Widget) -->
<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('fullPageChat', () => ({
        // Identical state variables as floating widget
        isOpen: true, // Always open on full page
        messages: [],
        newMessage: '',
        selectedFile: null,
        sending: false,
        unreadCount: 0,
        currentUserId: {{ auth()->id() }},
        supportUserId: null,
        currentChatId: null,
        pollingInterval: null,

        init() {
            this.loadUnreadCount();
            this.findSupportUser();
            this.startPolling();
        },

        // IDENTICAL to floating widget
        async findSupportUser() {
            try {
                const response = await fetch('{{ route("chat.users") }}');
                if (!response.ok) {
                    throw new Error(`HTTP ${response.status}`);
                }
                const contentType = response.headers.get('content-type');
                if (!contentType || !contentType.includes('application/json')) {
                    throw new Error('Invalid response format');
                }
                const data = await response.json();

                if (data.success && data.users.length > 0) {
                    // Find first admin user
                    const supportUser = data.users.find(user => user.is_admin);

                    if (supportUser) {
                        this.supportUserId = supportUser.id;
                    }
                }
            } catch (error) {
                console.error('Error finding support user:', error);
            } finally {
                // Always try to init chat, even if no specific admin found
                this.findOrCreateChat();
            }
        },

        // IDENTICAL to floating widget
        async findOrCreateChat() {
            try {
                // First, try to find existing active chat
                // First, try to find existing active chat
                const response = await fetch('{{ route("chat.index") }}', {
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });
                if (response.ok) {
                    const data = await response.json();
                    if (data.chats && data.chats.length > 0) {
                        this.currentChatId = data.chats[0].id;
                        this.loadMessages();
                        this.markAsRead();
                        return;
                    } else {
                        console.log('No existing chats found, creating new one...');
                    }
                } else {
                     console.error('Failed to fetch existing chats:', response.status);
                     alert('DEBUG WARNING: Failed to fetch existing chats (Status: ' + response.status + '). Will try to create new one.');
                }

                // If no existing chat, create a new one
                await this.createNewChat();
            } catch (error) {
                console.error('Error finding or creating chat:', error);
                alert('DEBUG ERROR: Exception in findOrCreateChat: ' + error.message);
            }
        },

        // IDENTICAL to floating widget
        async createNewChat() {
            try {
                const response = await fetch('{{ route("chat.start") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({
                        message: 'Hello! I need support.',
                        topic: 'General Support'
                    })
                });

                const data = await response.json();
                if (data.success) {
                    this.currentChatId = data.chat_id;
                    this.loadMessages();
                } else {
                    console.error('Failed to create chat:', data);
                    // alert('DEBUG ERROR: Server returned error: ' + JSON.stringify(data.errors || data.message || data));
                }
            } catch (error) {
                console.error('Error creating new chat:', error);
                // alert('DEBUG ERROR: Failed to create new chat session. ' + error.message);
            }
        },

        // IDENTICAL to floating widget
        async loadMessages() {
            if (!this.currentChatId) return;

            try {
                const response = await fetch(`{{ route('chat.messages', ['chat_id' => '__CHAT_ID__']) }}`.replace('__CHAT_ID__', this.currentChatId));
                if (!response.ok) {
                    throw new Error(`HTTP ${response.status}`);
                }
                const contentType = response.headers.get('content-type');
                if (!contentType || !contentType.includes('application/json')) {
                    throw new Error('Invalid response format');
                }
                const data = await response.json();

                if (data.success) {
                    this.messages = data.messages;
                    this.scrollToBottom();
                }
            } catch (error) {
                console.error('Error loading messages:', error);
            }
        },

        // IDENTICAL to floating widget
        async sendMessage() {
            if (!this.newMessage.trim() && !this.selectedFile) return;
            if (!this.currentChatId) return;

            this.sending = true;

            try {
                const formData = new FormData();
                formData.append('chat_id', this.currentChatId);
                formData.append('message', this.newMessage);

                if (this.selectedFile) {
                    formData.append('file', this.selectedFile);
                }

                const response = await fetch('{{ route("chat.send") }}', {
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

        // IDENTICAL to floating widget
        async markAsRead() {
            if (!this.currentChatId) return;

            try {
                await fetch('{{ route("chat.mark-read") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({ chat_id: this.currentChatId })
                });

                this.unreadCount = 0;
            } catch (error) {
                console.error('Error marking messages as read:', error);
            }
        },

        // IDENTICAL to floating widget
        startPolling() {
            // Poll for new messages every 3 seconds
            this.pollingInterval = setInterval(() => {
                if (this.isOpen && this.currentChatId) {
                    this.loadMessages();
                }
                this.loadUnreadCount();
            }, 3000);
        },

        // IDENTICAL to floating widget
        stopPolling() {
            if (this.pollingInterval) {
                clearInterval(this.pollingInterval);
                this.pollingInterval = null;
            }
        },

        // IDENTICAL to floating widget
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

        // IDENTICAL to floating widget
        async loadUnreadCount() {
            try {
                const response = await fetch('{{ route("chat.unread-count") }}');
                if (!response.ok) {
                    throw new Error(`HTTP ${response.status}`);
                }
                const contentType = response.headers.get('content-type');
                if (!contentType || !contentType.includes('application/json')) {
                    throw new Error('Invalid response format');
                }
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
                const container = document.getElementById('full-chat-messages');
                if (container) {
                    container.scrollTop = container.scrollHeight;
                }
            });
        },

        // IDENTICAL to floating widget
        formatTime(timestamp) {
            const date = new Date(timestamp);
            const now = new Date();
            const diff = now - date;

            if (diff < 60000) return 'Just now';
            if (diff < 3600000) return Math.floor(diff / 60000) + 'm ago';
            if (diff < 86400000) return Math.floor(diff / 3600000) + 'h ago';
            return date.toLocaleDateString();
        },

        // IDENTICAL to floating widget - Cleanup when component is destroyed
        destroy() {
            this.stopPolling();
        }
    }));
});
</script>
@endsection
