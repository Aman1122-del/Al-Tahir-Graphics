@extends('layouts.admin')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6">
                <div class="flex justify-between items-center mb-6">
                    <h1 class="text-3xl font-bold text-gray-900">Unified Chat Management</h1>
                    <div class="flex space-x-3">
                        <button onclick="exportChats()" class="px-4 py-2 bg-green-500 text-white rounded-md hover:bg-green-600">
                            Export to Excel
                        </button>
                        <button onclick="refreshStats()" class="px-4 py-2 bg-blue-500 text-white rounded-md hover:bg-blue-600">
                            Refresh Stats
                        </button>
                        <a href="{{ route('admin.chat.index') }}" class="px-4 py-2 bg-gray-500 text-white rounded-md hover:bg-gray-600">
                            Legacy Chat
                        </a>
                    </div>
                </div>

                <!-- Statistics Cards -->
                <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
                    <div class="bg-blue-100 p-6 rounded-lg">
                        <div class="flex items-center">
                            <div class="text-3xl font-bold text-blue-600" id="total-chats">{{ $chats->total() }}</div>
                            <div class="ml-4">
                                <div class="text-sm font-medium text-blue-600">Total Chats</div>
                                <div class="text-xs text-blue-500">All time</div>
                            </div>
                        </div>
                    </div>
                    <div class="bg-green-100 p-6 rounded-lg">
                        <div class="flex items-center">
                            <div class="text-3xl font-bold text-green-600" id="active-chats">{{ $chats->where('status', 'active')->count() }}</div>
                            <div class="ml-4">
                                <div class="text-sm font-medium text-green-600">Active Chats</div>
                                <div class="text-xs text-green-500">Currently open</div>
                            </div>
                        </div>
                    </div>
                    <div class="bg-yellow-100 p-6 rounded-lg">
                        <div class="flex items-center">
                            <div class="text-3xl font-bold text-yellow-600" id="unread-messages">{{ $chats->sum(function($chat) { return $chat->messages->where('is_read', false)->count(); }) }}</div>
                            <div class="ml-4">
                                <div class="text-sm font-medium text-yellow-600">Unread Messages</div>
                                <div class="text-xs text-yellow-500">Require attention</div>
                            </div>
                        </div>
                    </div>
                    <div class="bg-purple-100 p-6 rounded-lg">
                        <div class="flex items-center">
                            <div class="text-3xl font-bold text-purple-600" id="assigned-to-me">{{ $chats->where('assigned_to', auth()->id())->count() }}</div>
                            <div class="ml-4">
                                <div class="text-sm font-medium text-purple-600">Assigned to Me</div>
                                <div class="text-xs text-purple-500">My workload</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Filters -->
                <div class="bg-gray-50 p-4 rounded-lg mb-6">
                    <form method="GET" class="space-y-4">
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Status</label>
                                <select name="status" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                    <option value="">All Status</option>
                                    <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                                    <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                                    <option value="closed" {{ request('status') == 'closed' ? 'selected' : '' }}>Closed</option>
                                    <option value="archived" {{ request('status') == 'archived' ? 'selected' : '' }}>Archived</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Priority</label>
                                <select name="priority" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                    <option value="">All Priority</option>
                                    <option value="low" {{ request('priority') == 'low' ? 'selected' : '' }}>Low</option>
                                    <option value="normal" {{ request('priority') == 'normal' ? 'selected' : '' }}>Normal</option>
                                    <option value="high" {{ request('priority') == 'high' ? 'selected' : '' }}>High</option>
                                    <option value="urgent" {{ request('priority') == 'urgent' ? 'selected' : '' }}>Urgent</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Assigned To</label>
                                <select name="assigned_to" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                    <option value="">All Admins</option>
                                    <option value="unassigned" {{ request('assigned_to') == 'unassigned' ? 'selected' : '' }}>Unassigned</option>
                                    @foreach($admins as $admin)
                                        <option value="{{ $admin->id }}" {{ request('assigned_to') == $admin->id ? 'selected' : '' }}>
                                            {{ $admin->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Date Range</label>
                                <select name="date_range" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                    <option value="">All Time</option>
                                    <option value="today" {{ request('date_range') == 'today' ? 'selected' : '' }}>Today</option>
                                    <option value="week" {{ request('date_range') == 'week' ? 'selected' : '' }}>This Week</option>
                                    <option value="month" {{ request('date_range') == 'month' ? 'selected' : '' }}>This Month</option>
                                </select>
                            </div>
                        </div>
                        <div class="flex flex-wrap gap-4">
                            <div class="flex-1 min-w-64">
                                <label class="block text-sm font-medium text-gray-700">Search</label>
                                <input type="text" name="search" value="{{ request('search') }}" 
                                       placeholder="Search by title, participant name, or email..." 
                                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500">
                            </div>
                            <div class="flex items-end space-x-2">
                                <button type="submit" class="px-4 py-2 bg-blue-500 text-white rounded-md hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-500">
                                    <svg class="w-4 h-4 mr-2 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                    </svg>
                                    Filter
                                </button>
                                <a href="{{ route('admin.chat.unified.index') }}" class="px-4 py-2 bg-gray-500 text-white rounded-md hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-gray-500">
                                    <svg class="w-4 h-4 mr-2 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                                    </svg>
                                    Clear
                                </a>
                            </div>
                        </div>
                    </form>
                </div>

                <!-- Debug Info (Remove in production) -->
                <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 mb-6">
                    <h3 class="text-sm font-medium text-yellow-800 mb-2">Debug Info:</h3>
                    <p class="text-sm text-yellow-700">Total chats loaded: {{ $chats->count() }}</p>
                    <p class="text-sm text-yellow-700">Chats with participants: {{ $chats->filter(function($chat) { return $chat->participants->count() > 0; })->count() }}</p>
                    <p class="text-sm text-yellow-700">Total messages: {{ $chats->sum(function($chat) { return $chat->messages->count(); }) }}</p>
                </div>

                <!-- Mobile View (hidden on desktop) -->
                <div class="block md:hidden space-y-4">
                    @forelse($chats as $chat)
                        <div class="bg-white rounded-lg shadow p-4 border border-gray-200">
                            <div class="flex justify-between items-start mb-3">
                                <div>
                                    <h3 class="text-lg font-medium text-gray-900">{{ $chat->title }}</h3>
                                    <p class="text-sm text-gray-500">ID: {{ $chat->id }}</p>
                                </div>
                                @if($chat->messages->where('is_read', false)->count() > 0)
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                        {{ $chat->messages->where('is_read', false)->count() }} unread
                                    </span>
                                @endif
                            </div>
                            
                            <div class="space-y-2 mb-4">
                                <div class="flex items-center">
                                    <span class="text-sm font-medium text-gray-700">Participants:</span>
                                    <span class="ml-2 text-sm text-gray-600">
                                        @foreach($chat->participants->take(2) as $participant)
                                            {{ $participant->participant_name ?? $participant->user->name ?? 'Unknown' }}{{ !$loop->last ? ', ' : '' }}
                                        @endforeach
                                        @if($chat->participants->count() > 2)
                                            +{{ $chat->participants->count() - 2 }} more
                                        @endif
                                    </span>
                                </div>
                                
                                <div class="flex items-center">
                                    <span class="text-sm font-medium text-gray-700">Status:</span>
                                    <span class="ml-2 px-2 py-1 text-xs font-semibold rounded-full
                                        @if($chat->status == 'active') bg-green-100 text-green-800
                                        @elseif($chat->status == 'closed') bg-red-100 text-red-800
                                        @else bg-gray-100 text-gray-800 @endif">
                                        {{ ucfirst($chat->status) }}
                                    </span>
                                </div>
                                
                                <div class="flex items-center">
                                    <span class="text-sm font-medium text-gray-700">Assigned:</span>
                                    <span class="ml-2 text-sm text-gray-600">{{ $chat->assignedUser->name ?? 'Unassigned' }}</span>
                                </div>
                                
                                <div class="flex items-center">
                                    <span class="text-sm font-medium text-gray-700">Last Message:</span>
                                    <span class="ml-2 text-sm text-gray-600">
                                        @if($chat->last_message_at)
                                            {{ $chat->last_message_at->diffForHumans() }}
                                        @else
                                            No messages
                                        @endif
                                    </span>
                                </div>
                            </div>
                            
                            <div class="flex flex-wrap gap-2">
                                <a href="{{ route('admin.chat.unified.show', $chat) }}" 
                                   class="inline-flex items-center px-3 py-2 text-xs font-medium text-blue-600 hover:text-blue-900 bg-blue-50 hover:bg-blue-100 rounded">
                                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                    </svg>
                                    View
                                </a>
                                
                                @if($chat->messages->where('is_read', false)->count() > 0)
                                    <button onclick="markAsRead({{ $chat->id }})" 
                                            class="inline-flex items-center px-3 py-2 text-xs font-medium text-green-600 hover:text-green-900 bg-green-50 hover:bg-green-100 rounded">
                                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                        </svg>
                                        Mark Read
                                    </button>
                                @endif
                                
                                @if($chat->status == 'active')
                                    <button onclick="assignChat({{ $chat->id }})" 
                                            class="inline-flex items-center px-3 py-2 text-xs font-medium text-purple-600 hover:text-purple-900 bg-purple-50 hover:bg-purple-100 rounded">
                                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                        </svg>
                                        Assign
                                    </button>
                                @endif
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-8 text-gray-500">No chats found</div>
                    @endforelse
                </div>

                <!-- Desktop Table View (hidden on mobile) -->
                <div class="hidden md:block overflow-x-auto shadow ring-1 ring-black ring-opacity-5 rounded-lg">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Chat</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Participants</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Priority</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Assigned To</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Last Message</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($chats as $chat)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div>
                                                <div class="text-sm font-medium text-gray-900">{{ $chat->title }}</div>
                                                <div class="text-sm text-gray-500">ID: {{ $chat->id }}</div>
                                            </div>
                                            @if($chat->messages->where('is_read', false)->count() > 0)
                                                <span class="ml-2 inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                                    {{ $chat->messages->where('is_read', false)->count() }} unread
                                                </span>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900">
                                            @foreach($chat->participants->take(2) as $participant)
                                                <div>{{ $participant->participant_name ?? $participant->user->name ?? 'Unknown' }}</div>
                                            @endforeach
                                            @if($chat->participants->count() > 2)
                                                <div class="text-gray-500">+{{ $chat->participants->count() - 2 }} more</div>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                            @if($chat->status == 'active') bg-green-100 text-green-800
                                            @elseif($chat->status == 'closed') bg-red-100 text-red-800
                                            @else bg-gray-100 text-gray-800 @endif">
                                            {{ ucfirst($chat->status) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                            @if($chat->priority == 'urgent') bg-red-100 text-red-800
                                            @elseif($chat->priority == 'high') bg-orange-100 text-orange-800
                                            @elseif($chat->priority == 'normal') bg-blue-100 text-blue-800
                                            @else bg-gray-100 text-gray-800 @endif">
                                            {{ ucfirst($chat->priority) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ $chat->assignedUser->name ?? 'Unassigned' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        @if($chat->last_message_at)
                                            {{ $chat->last_message_at->diffForHumans() }}
                                        @else
                                            No messages
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 text-sm font-medium">
                                        <div class="flex flex-wrap gap-1 sm:gap-2">
                                            <!-- View/Open Chat -->
                                            <a href="{{ route('admin.chat.unified.show', $chat) }}" 
                                               class="inline-flex items-center px-2 py-1 text-xs font-medium text-blue-600 hover:text-blue-900 bg-blue-50 hover:bg-blue-100 rounded">
                                                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                                </svg>
                                                View
                                            </a>
                                            
                                            <!-- Mark Read/Unread -->
                                            @if($chat->messages->where('is_read', false)->count() > 0)
                                                <button onclick="markAsRead({{ $chat->id }})" 
                                                        class="inline-flex items-center px-2 py-1 text-xs font-medium text-green-600 hover:text-green-900 bg-green-50 hover:bg-green-100 rounded">
                                                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                                    </svg>
                                                    Mark Read
                                                </button>
                                            @else
                                                <button onclick="markAsUnread({{ $chat->id }})" 
                                                        class="inline-flex items-center px-2 py-1 text-xs font-medium text-yellow-600 hover:text-yellow-900 bg-yellow-50 hover:bg-yellow-100 rounded">
                                                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                    </svg>
                                                    Mark Unread
                                                </button>
                                            @endif
                                            
                                            <!-- Assign Chat -->
                                            @if($chat->status == 'active')
                                                <button onclick="assignChat({{ $chat->id }})" 
                                                        class="inline-flex items-center px-2 py-1 text-xs font-medium text-purple-600 hover:text-purple-900 bg-purple-50 hover:bg-purple-100 rounded">
                                                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                                    </svg>
                                                    Assign
                                                </button>
                                                
                                                <!-- Toggle Active Status -->
                                                <button onclick="toggleActive({{ $chat->id }})" 
                                                        class="inline-flex items-center px-2 py-1 text-xs font-medium text-orange-600 hover:text-orange-900 bg-orange-50 hover:bg-orange-100 rounded">
                                                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 9l4-4 4 4m0 6l-4 4-4-4"></path>
                                                    </svg>
                                                    Toggle
                                                </button>
                                                
                                                <!-- Close Chat -->
                                                <button onclick="closeChat({{ $chat->id }})" 
                                                        class="inline-flex items-center px-2 py-1 text-xs font-medium text-red-600 hover:text-red-900 bg-red-50 hover:bg-red-100 rounded">
                                                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                                    </svg>
                                                    Close
                                                </button>
                                            @endif
                                            
                                            <!-- Archive Chat -->
                                            @if($chat->status == 'closed')
                                                <button onclick="archiveChat({{ $chat->id }})" 
                                                        class="inline-flex items-center px-2 py-1 text-xs font-medium text-gray-600 hover:text-gray-900 bg-gray-50 hover:bg-gray-100 rounded">
                                                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8l6 6 6-6"></path>
                                                    </svg>
                                                    Archive
                                                </button>
                                            @endif
                                            
                                            <!-- Delete Chat -->
                                            <button onclick="deleteChat({{ $chat->id }})" 
                                                    class="inline-flex items-center px-2 py-1 text-xs font-medium text-red-600 hover:text-red-900 bg-red-50 hover:bg-red-100 rounded">
                                                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                </svg>
                                                Delete
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-6 py-4 text-center text-gray-500">No chats found</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                </div>

                <!-- Pagination -->
                <div class="mt-6">
                    {{ $chats->links() }}
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Assign Chat Modal -->
<div id="assignModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden">
    <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
            <form id="assignForm">
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">Assign Chat</h3>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Assign to Admin</label>
                        <select id="adminSelect" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                            <option value="">Select Admin</option>
                            @foreach($admins as $admin)
                                <option value="{{ $admin->id }}">{{ $admin->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                    <button type="submit" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 sm:ml-3 sm:w-auto sm:text-sm">
                        Assign
                    </button>
                    <button type="button" onclick="closeAssignModal()" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                        Cancel
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
let currentChatId = null;
let stats = {};

// Load statistics
async function loadStats() {
    try {
        console.log('Loading statistics...');
        const response = await fetch('{{ route("admin.chat.unified.statistics") }}', {
            method: 'GET',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json',
            }
        });
        
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        
        const data = await response.json();
        console.log('Statistics response:', data);
        
        if (data.success) {
            stats = data.stats;
            updateStatsDisplay();
            console.log('Statistics updated successfully');
        } else {
            console.error('Statistics API returned success: false');
        }
    } catch (error) {
        console.error('Error loading stats:', error);
        console.log('Falling back to server-side stats display');
    }
}

// Update stats display
function updateStatsDisplay() {
    document.getElementById('total-chats').textContent = stats.total_chats || 0;
    document.getElementById('active-chats').textContent = stats.active_chats || 0;
    document.getElementById('unread-messages').textContent = stats.unread_messages || 0;
    document.getElementById('assigned-to-me').textContent = stats.assigned_to_me || 0;
}

function refreshStats() {
    loadStats();
}

function exportChats() {
    const params = new URLSearchParams(window.location.search);
    window.open(`{{ route('admin.chat.unified.export') }}?${params}`, '_blank');
}

function assignChat(chatId) {
    currentChatId = chatId;
    document.getElementById('assignModal').classList.remove('hidden');
}

function closeAssignModal() {
    document.getElementById('assignModal').classList.add('hidden');
    currentChatId = null;
}

function closeChat(chatId) {
    if (!chatId) {
        alert('Error: Chat ID is missing. Please refresh the page and try again.');
        return;
    }
    
    if (confirm('Are you sure you want to close this chat?')) {
        fetch(`/admin/chat/unified/${chatId}/close`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Content-Type': 'application/json',
            },
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('Error closing chat');
            }
        });
    }
}

function archiveChat(chatId) {
    if (!chatId) {
        alert('Error: Chat ID is missing. Please refresh the page and try again.');
        return;
    }
    
    if (confirm('Are you sure you want to archive this chat?')) {
        fetch(`/admin/chat/unified/${chatId}/archive`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Content-Type': 'application/json',
            },
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('Error archiving chat');
            }
        });
    }
}

function markAsRead(chatId) {
    if (!chatId) {
        alert('Error: Chat ID is missing. Please refresh the page and try again.');
        return;
    }
    
    fetch(`/admin/chat/unified/${chatId}/mark-read`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Content-Type': 'application/json',
        },
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert('Error marking chat as read');
        }
    });
}

function markAsUnread(chatId) {
    if (!chatId) {
        alert('Error: Chat ID is missing. Please refresh the page and try again.');
        return;
    }
    
    fetch(`/admin/chat/unified/${chatId}/mark-unread`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Content-Type': 'application/json',
        },
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert('Error marking chat as unread');
        }
    });
}

function toggleActive(chatId) {
    if (!chatId) {
        alert('Error: Chat ID is missing. Please refresh the page and try again.');
        return;
    }
    
    fetch(`/admin/chat/unified/${chatId}/toggle-active`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Content-Type': 'application/json',
        },
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert('Error toggling chat status');
        }
    });
}

function deleteChat(chatId) {
    if (!chatId) {
        alert('Error: Chat ID is missing. Please refresh the page and try again.');
        return;
    }
    
    if (confirm('Are you sure you want to delete this chat? This action cannot be undone.')) {
        fetch(`/admin/chat/unified/${chatId}/delete`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Content-Type': 'application/json',
            },
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('Error deleting chat');
            }
        });
    }
}

document.getElementById('assignForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const adminId = document.getElementById('adminSelect').value;
    if (!adminId) {
        alert('Please select an admin');
        return;
    }

    fetch(`/admin/chat/unified/${currentChatId}/assign`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({ admin_id: adminId }),
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert('Error assigning chat');
        }
    });
});

// Load stats on page load
document.addEventListener('DOMContentLoaded', function() {
    loadStats();
    
    // Set up polling updates
    setupPollingUpdates();
});

// Polling updates for chat data
function setupPollingUpdates() {
    // Auto-refresh stats every 30 seconds
    setInterval(loadStats, 30000);
    
    // Auto-refresh chat list every 60 seconds
    setInterval(function() {
        location.reload();
    }, 60000);
}
</script>
@endsection
