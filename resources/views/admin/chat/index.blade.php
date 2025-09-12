@extends('layouts.admin')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6">
                <div class="flex justify-between items-center mb-6">
                    <h1 class="text-3xl font-bold text-gray-900">Chat Management</h1>
                    <div class="flex space-x-3">
                        <button @click="exportChats()" class="px-4 py-2 bg-green-500 text-white rounded-md hover:bg-green-600">
                            Export Chats
                        </button>
                        <button @click="refreshStats()" class="px-4 py-2 bg-blue-500 text-white rounded-md hover:bg-blue-600">
                            Refresh Stats
                        </button>
                    </div>
                </div>

                <!-- Statistics Cards -->
                <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
                    <div class="bg-blue-50 p-6 rounded-lg border border-blue-200">
                        <div class="flex items-center">
                            <div class="p-2 bg-blue-500 rounded-lg">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                                </svg>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-blue-600">Total Messages</p>
                                <p class="text-2xl font-bold text-blue-900" x-text="stats.total_messages || 0">0</p>
                            </div>
                        </div>
                    </div>

                    <div class="bg-green-50 p-6 rounded-lg border border-green-200">
                        <div class="flex items-center">
                            <div class="p-2 bg-green-500 rounded-lg">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
                                </svg>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-green-600">Active Users</p>
                                <p class="text-2xl font-bold text-green-900" x-text="stats.total_users_with_chats || 0">0</p>
                            </div>
                        </div>
                    </div>

                    <div class="bg-yellow-50 p-6 rounded-lg border border-yellow-200">
                        <div class="flex items-center">
                            <div class="p-2 bg-yellow-500 rounded-lg">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-yellow-600">Unread Messages</p>
                                <p class="text-2xl font-bold text-yellow-900" x-text="stats.unread_messages || 0">0</p>
                            </div>
                        </div>
                    </div>

                    <div class="bg-purple-50 p-6 rounded-lg border border-purple-200">
                        <div class="flex items-center">
                            <div class="p-2 bg-purple-500 rounded-lg">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                </svg>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-purple-600">Today's Messages</p>
                                <p class="text-2xl font-bold text-purple-900" x-text="stats.messages_today || 0">0</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Search and Filters -->
                <div class="mb-6">
                    <div class="flex space-x-4">
                        <div class="flex-1">
                            <input type="text" 
                                   placeholder="Search users..." 
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                                   x-model="searchQuery"
                                   @input="searchUsers()">
                        </div>
                        <div class="flex space-x-2">
                            <select x-model="statusFilter" @change="filterUsers()" class="px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <option value="">All Status</option>
                                <option value="active">Active</option>
                                <option value="inactive">Inactive</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Chat Users Table -->
                <div class="bg-white shadow overflow-hidden sm:rounded-md">
                    <ul class="divide-y divide-gray-200">
                        <template x-for="user in filteredUsers" :key="user.id">
                            <li class="px-6 py-4 hover:bg-gray-50 cursor-pointer" @click="openChat(user)">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center">
                                        <!-- Avatar -->
                                        <div class="w-10 h-10 bg-blue-500 rounded-full flex items-center justify-center text-white font-semibold mr-4">
                                            <span x-text="user.name.charAt(0).toUpperCase()"></span>
                                        </div>
                                        
                                        <!-- User Info -->
                                        <div>
                                            <p class="text-sm font-medium text-gray-900" x-text="user.name"></p>
                                            <p class="text-sm text-gray-500" x-text="user.email"></p>
                                        </div>
                                    </div>
                                    
                                    <!-- Chat Stats -->
                                    <div class="flex items-center space-x-4">
                                        <div class="text-right">
                                            <p class="text-sm text-gray-900" x-text="user.sent_messages_count + ' sent'"></p>
                                            <p class="text-sm text-gray-500" x-text="user.received_messages_count + ' received'"></p>
                                        </div>
                                        
                                        <!-- Unread Badge -->
                                        <div x-show="user.unread_count > 0" 
                                             class="bg-red-500 text-white text-xs rounded-full h-6 w-6 flex items-center justify-center">
                                            <span x-text="user.unread_count"></span>
                                        </div>
                                        
                                        <!-- Status Indicator -->
                                        <div class="w-3 h-3 rounded-full" 
                                             :class="{ 
                                                 'bg-green-500': user.last_activity && new Date(user.last_activity) > new Date(Date.now() - 5 * 60 * 1000),
                                                 'bg-gray-400': !user.last_activity || new Date(user.last_activity) <= new Date(Date.now() - 5 * 60 * 1000)
                                             }"></div>
                                    </div>
                                </div>
                            </li>
                        </template>
                    </ul>
                    
                    <div x-show="filteredUsers.length === 0" class="text-center text-gray-500 py-8">
                        No users found
                    </div>
                </div>

                <!-- Recent Conversations -->
                <div class="mt-8">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Recent Conversations</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                        <template x-for="(conversation, userId) in recentConversations" :key="userId">
                            <div class="bg-gray-50 p-4 rounded-lg border border-gray-200 hover:bg-gray-100 cursor-pointer" @click="openChat({id: userId})">
                                <div class="flex items-center justify-between mb-2">
                                    <span class="text-sm font-medium text-gray-900" x-text="getUserName(userId)"></span>
                                    <span class="text-xs text-gray-500" x-text="formatTime(conversation[0].created_at)"></span>
                                </div>
                                <p class="text-sm text-gray-600 truncate" x-text="conversation[0].message"></p>
                            </div>
                        </template>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Export Modal -->
<div x-show="showExportModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Export Chat Data</h3>
            
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Date Range</label>
                <div class="grid grid-cols-2 gap-2">
                    <input type="date" x-model="exportStartDate" class="px-3 py-2 border border-gray-300 rounded-md">
                    <input type="date" x-model="exportEndDate" class="px-3 py-2 border border-gray-300 rounded-md">
                </div>
            </div>
            
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Format</label>
                <select x-model="exportFormat" class="w-full px-3 py-2 border border-gray-300 rounded-md">
                    <option value="csv">CSV</option>
                    <option value="excel">Excel</option>
                </select>
            </div>
            
            <div class="flex justify-end space-x-3">
                <button @click="showExportModal = false" class="px-4 py-2 text-gray-600 hover:text-gray-800">
                    Cancel
                </button>
                <button @click="performExport()" class="px-4 py-2 bg-blue-500 text-white rounded-md hover:bg-blue-600">
                    Export
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Data Initialization -->
<script>
window.chatData = {
    users: @json($chatUsers),
    recentConversations: @json($recentConversations)
};
</script>

<!-- Alpine.js Logic -->
<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('adminChat', () => ({
        users: window.chatData.users,
        filteredUsers: window.chatData.users,
        recentConversations: window.chatData.recentConversations,
        stats: {},
        searchQuery: '',
        statusFilter: '',
        showExportModal: false,
        exportStartDate: '',
        exportEndDate: '',
        exportFormat: 'csv',
        
        init() {
            this.loadStats();
            this.exportStartDate = new Date().toISOString().split('T')[0];
            this.exportEndDate = new Date().toISOString().split('T')[0];
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
        
        searchUsers() {
            if (!this.searchQuery.trim()) {
                this.filteredUsers = this.users;
                return;
            }
            
            this.filteredUsers = this.users.filter(user => 
                user.name.toLowerCase().includes(this.searchQuery.toLowerCase()) ||
                user.email.toLowerCase().includes(this.searchQuery.toLowerCase())
            );
        },
        
        filterUsers() {
            let filtered = this.users;
            
            if (this.searchQuery.trim()) {
                filtered = filtered.filter(user => 
                    user.name.toLowerCase().includes(this.searchQuery.toLowerCase()) ||
                    user.email.toLowerCase().includes(this.searchQuery.toLowerCase())
                );
            }
            
            if (this.statusFilter) {
                // Add status filtering logic here
            }
            
            this.filteredUsers = filtered;
        },
        
        openChat(user) {
            window.location.href = `/admin/chat/${user.id}`;
        },
        
        getUserName(userId) {
            const user = this.users.find(u => u.id == userId);
            return user ? user.name : 'Unknown User';
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
        
        exportChats() {
            this.showExportModal = true;
        },
        
        async performExport() {
            const params = new URLSearchParams({
                format: this.exportFormat,
                start_date: this.exportStartDate,
                end_date: this.exportEndDate
            });
            
            window.open(`/admin/chat/export?${params}`, '_blank');
            this.showExportModal = false;
        },
        
        refreshStats() {
            this.loadStats();
        }
    }));
});
</script>
@endsection
