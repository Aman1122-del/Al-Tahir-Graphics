<div x-data="chatWidget()" class="fixed bottom-4 right-4 z-50">
    <!-- Chat Widget Toggle Button -->
    <button @click="toggleWidget()"
            class="w-14 h-14 bg-blue-500 hover:bg-blue-600 text-white rounded-full shadow-lg transition-all duration-200 flex items-center justify-center"
            :class="{ 'scale-110': isOpen }">
        <div x-show="!isOpen" class="relative">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
            </svg>
            <div x-show="unreadCount > 0"
                 class="absolute -top-2 -right-2 bg-red-500 text-white text-xs rounded-full h-5 w-5 flex items-center justify-center">
                <span x-text="unreadCount"></span>
            </div>
        </div>
        <div x-show="isOpen" class="relative">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
        </div>
    </button>

    <!-- Chat Widget Panel -->
    <div x-show="isOpen"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 transform scale-95 translate-y-4"
         x-transition:enter-end="opacity-100 transform scale-100 translate-y-0"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100 transform scale-100 translate-y-0"
         x-transition:leave-end="opacity-0 transform scale-95 translate-y-4"
         class="absolute bottom-16 right-0 w-80 h-96 bg-white rounded-lg shadow-xl border border-gray-200 overflow-hidden">

        <!-- Chat Header -->
        <div class="bg-blue-500 text-white p-4">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-2">
                    <div class="w-8 h-8 bg-white bg-opacity-20 rounded-full flex items-center justify-center">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                        </svg>
                    </div>
                    <div>
                        <h3 class="font-semibold">Support Chat</h3>
                        <p class="text-sm opacity-90">We're here to help!</p>
                    </div>
                </div>
                <button @click="minimizeWidget()" class="text-white hover:text-gray-200">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                    </svg>
                </button>
            </div>
        </div>

        <!-- Chat Messages -->
        <div class="h-64 p-3 overflow-y-auto bg-gray-50" id="widget-messages">
            <div x-show="messages.length === 0" class="text-center text-gray-500 py-8">
                <p class="mb-2">👋 Hi there!</p>
                <p class="text-sm">How can we help you today?</p>
            </div>

            <template x-for="message in messages" :key="message.id">
                <div class="mb-3" :class="{ 'text-right': message.sender_id === currentUserId }">
                    <div class="inline-block max-w-xs px-3 py-2 rounded-lg"
                         :class="{
                             'bg-blue-500 text-white': message.sender_id === currentUserId,
                             'bg-white text-gray-900 border border-gray-200': message.sender_id !== currentUserId
                         }">
                        <div class="text-xs opacity-75 mb-1"
                             x-text="message.sender_id === currentUserId ? 'You' : 'Support'">
                        </div>
                        <div class="text-sm" x-text="message.message"></div>
                        <div class="text-xs opacity-75 mt-1"
                             x-text="formatTime(message.created_at)">
                        </div>
                    </div>
                </div>
            </template>
        </div>

        <!-- Chat Input -->
        <div class="p-3 bg-white border-t border-gray-200">
            <form @submit.prevent="sendMessage()" class="flex space-x-2">
                <input type="text"
                       x-model="newMessage"
                       placeholder="Type your message..."
                       class="flex-1 px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm"
                       :disabled="sending || !chatId">

                <button type="submit"
                        :disabled="!newMessage.trim() || sending || !chatId"
                        class="px-3 py-2 bg-blue-500 text-white rounded-md hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-500 disabled:opacity-50 disabled:cursor-not-allowed">
                    <span x-show="!sending">Send</span>
                    <span x-show="sending">...</span>
                </button>
            </form>

            <div x-show="!chatId" class="text-center mt-2">
                <button @click="startChat()"
                        class="text-xs text-blue-500 hover:text-blue-600 underline">
                    Start a conversation
                </button>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('chatWidget', () => ({
        isOpen: false,
        isMinimized: false,
        messages: [],
        newMessage: '',
        sending: false,
        chatId: null,
        currentUserId: {{ auth()->check() ? auth()->id() : 'null' }},
        unreadCount: 0,
        pollingInterval: null,

        init() {
            this.checkExistingChat();
            this.startPolling();
            this.loadUnreadCount();
        },

        toggleWidget() {
            this.isOpen = !this.isOpen;
            if (this.isOpen) {
                // If no chat exists, start one
                if (!this.chatId) {
                    this.startChat();
                } else {
                    this.loadMessages();
                }
                this.scrollToBottom();
            }
        },

        minimizeWidget() {
            this.isOpen = false;
        },

        async checkExistingChat() {
            try {
                const response = await fetch('{{ route("chat.index") }}', {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    }
                });
                if (response.ok) {
                    const data = await response.json();
                    if (data.success && data.chats && data.chats.length > 0) {
                        this.chatId = data.chats[0].id;
                        this.loadMessages();
                    }
                }
            } catch (error) {
                console.error('Error checking existing chat:', error);
            }
        },

        async startChat() {
            if (!this.currentUserId) {
                alert('Please log in to start a chat');
                return;
            }

            try {
                const response = await fetch('{{ route("chat.start") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({
                        message: 'Hello, I need help!',
                        topic: 'general'
                    })
                });

                const data = await response.json();
                if (data.success) {
                    this.chatId = data.chat_id;
                    this.loadMessages();
                }
            } catch (error) {
                console.error('Error starting chat:', error);
            }
        },

        async loadMessages() {
            if (!this.chatId) return;

            try {
                const response = await fetch(`{{ route('chat.messages', ['chat_id' => '__CHAT_ID__']) }}`.replace('__CHAT_ID__', this.chatId), {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    }
                });
                if (response.ok) {
                    const data = await response.json();
                    if (data.success) {
                        this.messages = data.messages;
                        this.scrollToBottom();
                    }
                }
            } catch (error) {
                console.error('Error loading messages:', error);
            }
        },

        async sendMessage() {
            if (!this.newMessage.trim()) return;

            // If no chat exists, start one first
            if (!this.chatId) {
                await this.startChat();
            }

            if (!this.chatId) {
                alert('Unable to start chat. Please try again.');
                return;
            }

            this.sending = true;

            try {
                const formData = new FormData();
                formData.append('chat_id', this.chatId);
                formData.append('message', this.newMessage);

                const response = await fetch('{{ route("chat.send") }}', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: formData
                });

                const data = await response.json();
                if (data.success) {
                    this.messages.push(data.message);
                    this.newMessage = '';
                    this.scrollToBottom();
                }
            } catch (error) {
                console.error('Error sending message:', error);
                alert('Error sending message. Please try again.');
            } finally {
                this.sending = false;
            }
        },

        startPolling() {
            this.pollingInterval = setInterval(() => {
                this.checkForNewMessages();
                this.loadUnreadCount();
            }, 5000);
        },

        stopPolling() {
            if (this.pollingInterval) {
                clearInterval(this.pollingInterval);
            }
        },

        async checkForNewMessages() {
            if (!this.chatId) return;

            try {
                const lastMessageId = this.messages.length > 0 ? this.messages[this.messages.length - 1].id : 0;
                const response = await fetch(`{{ route('chat.messages', ['chat_id' => '__CHAT_ID__']) }}?since=${lastMessageId}`.replace('__CHAT_ID__', this.chatId), {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    }
                });
                if (response.ok) {
                    const data = await response.json();
                    if (data.success && data.messages && data.messages.length > 0) {
                        this.messages.push(...data.messages);
                        this.scrollToBottom();
                        this.loadUnreadCount();
                    }
                }
            } catch (error) {
                console.error('Error checking for new messages:', error);
            }
        },

        async loadUnreadCount() {
            try {
                const response = await fetch('{{ route("chat.unread-count") }}', {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    }
                });
                if (response.ok) {
                    const data = await response.json();
                    if (data.success) {
                        this.unreadCount = data.count;
                    }
                }
            } catch (error) {
                console.error('Error loading unread count:', error);
            }
        },

        scrollToBottom() {
            this.$nextTick(() => {
                const container = document.getElementById('widget-messages');
                container.scrollTop = container.scrollHeight;
            });
        },

        formatTime(timestamp) {
            const date = new Date(timestamp);
            return date.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
        },

        destroy() {
            this.stopPolling();
        }
    }));
});
</script>
