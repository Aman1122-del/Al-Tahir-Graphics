@extends('layouts.admin')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6">
                <!-- Chat Header -->
                <div class="flex justify-between items-center mb-6">
                    <div>
                        <h1 class="text-3xl font-bold text-gray-900">{{ $chat->title }}</h1>
                        <div class="flex items-center space-x-4 mt-2">
                            <span class="px-2 py-1 text-xs font-semibold rounded-full
                                @if($chat->status == 'active') bg-green-100 text-green-800
                                @elseif($chat->status == 'closed') bg-red-100 text-red-800
                                @else bg-gray-100 text-gray-800 @endif">
                                {{ ucfirst($chat->status) }}
                            </span>
                            <span class="px-2 py-1 text-xs font-semibold rounded-full
                                @if($chat->priority == 'urgent') bg-red-100 text-red-800
                                @elseif($chat->priority == 'high') bg-orange-100 text-orange-800
                                @elseif($chat->priority == 'normal') bg-blue-100 text-blue-800
                                @else bg-gray-100 text-gray-800 @endif">
                                {{ ucfirst($chat->priority) }}
                            </span>
                            <span class="text-sm text-gray-500">
                                Assigned to: {{ $chat->assignedUser->name ?? 'Unassigned' }}
                            </span>
                        </div>
                    </div>
                    <div class="flex space-x-3">
                        @if($chat->status == 'active')
                            <button onclick="closeChat({{ $chat->id }})" 
                                    class="px-4 py-2 bg-red-500 text-white rounded-md hover:bg-red-600">
                                Close Chat
                            </button>
                        @endif
                        <a href="{{ route('admin.chat.unified.index') }}" 
                           class="px-4 py-2 bg-gray-500 text-white rounded-md hover:bg-gray-600">
                            Back to List
                        </a>
                    </div>
                </div>

                <!-- Participants Info -->
                <div class="bg-gray-50 p-4 rounded-lg mb-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-2">Participants</h3>
                    <div class="flex flex-wrap gap-2">
                        @foreach($chat->participants as $participant)
                            <span class="px-3 py-1 bg-blue-100 text-blue-800 text-sm rounded-full">
                                {{ $participant->participant_name ?? $participant->user->name ?? 'Unknown' }}
                                @if($participant->role != 'participant')
                                    ({{ ucfirst($participant->role) }})
                                @endif
                            </span>
                        @endforeach
                    </div>
                </div>

                <!-- Chat Messages -->
                <div class="bg-white border border-gray-200 rounded-lg mb-6 relative">
                    <!-- Loading indicator -->
                    <div id="loadingIndicator" class="hidden absolute top-4 left-1/2 transform -translate-x-1/2 bg-blue-100 text-blue-800 px-3 py-1 rounded-full text-sm">
                        Loading messages...
                    </div>
                    
                    <!-- Messages container -->
                    <div class="h-96 overflow-y-auto p-4" id="chatMessages">
                        @foreach($messages as $message)
                            <div class="mb-4 {{ $message->sender_id == Auth::id() ? 'flex justify-end' : 'flex justify-start' }}">
                                <div class="max-w-xs lg:max-w-md px-4 py-2 rounded-lg {{ $message->sender_id == Auth::id() ? 'bg-blue-500 text-white' : 'bg-gray-200 text-gray-800' }}">
                                    <div class="flex items-center space-x-2 mb-1">
                                        <span class="text-xs font-medium opacity-75">
                                            {{ $message->sender->name ?? 'Unknown' }}
                                        </span>
                                        <span class="text-xs opacity-75">
                                            {{ $message->created_at->format('g:i A') }}
                                        </span>
                                        @if($message->is_edited)
                                            <span class="text-xs opacity-75">(edited)</span>
                                        @endif
                                    </div>
                                    <div class="text-sm">{{ $message->message }}</div>
                                    
                                    @if($message->file_path)
                                        <div class="mt-2">
                                            <a href="{{ Storage::url($message->file_path) }}" target="_blank" 
                                               class="inline-flex items-center px-2 py-1 {{ $message->sender_id == Auth::id() ? 'bg-blue-600 text-white' : 'bg-gray-300 text-gray-700' }} rounded text-xs hover:opacity-80">
                                                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"></path>
                                                </svg>
                                                {{ $message->file_name }}
                                            </a>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                    
                    <!-- Pagination for older messages -->
                    <div class="border-t border-gray-200 p-4">
                        <div class="flex justify-center">
                            {{ $messages->links() }}
                        </div>
                    </div>
                </div>

                <!-- Reply Form -->
                @if($chat->status == 'active')
                    <form id="replyForm" class="bg-gray-50 p-4 rounded-lg">
                        @csrf
                        <div class="flex space-x-4">
                            <div class="flex-1">
                                <textarea name="message" id="messageInput" rows="3" 
                                          class="w-full rounded-md border-gray-300 shadow-sm" 
                                          placeholder="Type your reply..." required></textarea>
                            </div>
                            <div class="flex flex-col space-y-2">
                                <input type="file" id="fileInput" name="file" accept="image/*,.pdf,.doc,.docx" 
                                       class="hidden">
                                <button type="button" onclick="document.getElementById('fileInput').click()" 
                                        class="px-4 py-2 bg-gray-500 text-white rounded-md hover:bg-gray-600">
                                    Attach
                                </button>
                                <button type="submit" 
                                        class="px-4 py-2 bg-blue-500 text-white rounded-md hover:bg-blue-600">
                                    Send
                                </button>
                            </div>
                        </div>
                        <div id="fileInfo" class="mt-2 text-sm text-gray-600 hidden"></div>
                    </form>
                @else
                    <div class="bg-gray-100 p-4 rounded-lg text-center text-gray-500">
                        This chat is {{ $chat->status }}. You cannot send messages.
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<script>
let pollingInterval;
let lastMessageId = {{ $messages->count() > 0 ? $messages->last()->id : 0 }};
let isPolling = false;

// Auto-scroll to bottom
function scrollToBottom() {
    const chatMessages = document.getElementById('chatMessages');
    chatMessages.scrollTop = chatMessages.scrollHeight;
}

// Load new messages via AJAX
async function loadNewMessages() {
    if (isPolling) return;
    isPolling = true;
    
    try {
        const response = await fetch(`/admin/chat/unified/{{ $chat->id }}/messages?since=${lastMessageId}`, {
            method: 'GET',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json',
            }
        });
        
        if (response.ok) {
            const data = await response.json();
            if (data.success && data.messages.length > 0) {
                appendMessages(data.messages);
                lastMessageId = data.messages[data.messages.length - 1].id;
                scrollToBottom();
            }
        }
    } catch (error) {
        console.log('Error loading new messages:', error);
    } finally {
        isPolling = false;
    }
}

// Append new messages to the chat
function appendMessages(messages) {
    const chatMessages = document.getElementById('chatMessages');
    
    messages.forEach(message => {
        const messageDiv = document.createElement('div');
        messageDiv.className = `mb-4 ${message.sender_id == {{ Auth::id() }} ? 'flex justify-end' : 'flex justify-start'}`;
        
        const isAdmin = message.sender_id == {{ Auth::id() }};
        const bgColor = isAdmin ? 'bg-blue-500 text-white' : 'bg-gray-200 text-gray-800';
        const fileBgColor = isAdmin ? 'bg-blue-600 text-white' : 'bg-gray-300 text-gray-700';
        
        messageDiv.innerHTML = `
            <div class="max-w-xs lg:max-w-md px-4 py-2 rounded-lg ${bgColor}">
                <div class="flex items-center space-x-2 mb-1">
                    <span class="text-xs font-medium opacity-75">
                        ${message.sender ? message.sender.name : 'Unknown'}
                    </span>
                    <span class="text-xs opacity-75">
                        ${new Date(message.created_at).toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'})}
                    </span>
                    ${message.is_edited ? '<span class="text-xs opacity-75">(edited)</span>' : ''}
                </div>
                <div class="text-sm">${message.message}</div>
                ${message.file_path ? `
                    <div class="mt-2">
                        <a href="/storage/${message.file_path}" target="_blank" 
                           class="inline-flex items-center px-2 py-1 ${fileBgColor} rounded text-xs hover:opacity-80">
                            <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"></path>
                            </svg>
                            ${message.file_name}
                        </a>
                    </div>
                ` : ''}
            </div>
        `;
        
        chatMessages.appendChild(messageDiv);
    });
}

// Start polling for new messages
function startPolling() {
    pollingInterval = setInterval(loadNewMessages, 3000); // Poll every 3 seconds
}

// Stop polling
function stopPolling() {
    if (pollingInterval) {
        clearInterval(pollingInterval);
        pollingInterval = null;
    }
}

// Scroll to bottom on page load
document.addEventListener('DOMContentLoaded', function() {
    scrollToBottom();
    startPolling();
});

// Stop polling when page is hidden
document.addEventListener('visibilitychange', function() {
    if (document.hidden) {
        stopPolling();
    } else {
        startPolling();
    }
});

// File input change handler
document.getElementById('fileInput').addEventListener('change', function(e) {
    const file = e.target.files[0];
    const fileInfo = document.getElementById('fileInfo');
    
    if (file) {
        fileInfo.textContent = `Selected: ${file.name} (${(file.size / 1024).toFixed(1)} KB)`;
        fileInfo.classList.remove('hidden');
    } else {
        fileInfo.classList.add('hidden');
    }
});

// Reply form submission
document.getElementById('replyForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    const messageInput = document.getElementById('messageInput');
    
    if (!messageInput.value.trim()) {
        alert('Please enter a message');
        return;
    }
    
    fetch(`/admin/chat/unified/{{ $chat->id }}/reply`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
        },
        body: formData,
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Add message to chat
            addMessageToChat(data.message);
            messageInput.value = '';
            document.getElementById('fileInput').value = '';
            document.getElementById('fileInfo').classList.add('hidden');
        } else {
            alert('Error sending message');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error sending message');
    });
});

// Add message to chat display
function addMessageToChat(message) {
    const chatMessages = document.getElementById('chatMessages');
    const messageDiv = document.createElement('div');
    messageDiv.className = 'mb-4 flex justify-end';
    
    const now = new Date();
    const timeString = now.toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'});
    
    messageDiv.innerHTML = `
        <div class="max-w-xs lg:max-w-md px-4 py-2 rounded-lg bg-blue-500 text-white">
            <div class="flex items-center space-x-2 mb-1">
                <span class="text-xs font-medium opacity-75">
                    ${message.sender ? message.sender.name : 'You'}
                </span>
                <span class="text-xs opacity-75">${timeString}</span>
            </div>
            <div class="text-sm">${message.message}</div>
            ${message.file_path ? `
                <div class="mt-2">
                    <a href="/storage/${message.file_path}" target="_blank" 
                       class="inline-flex items-center px-2 py-1 bg-blue-600 text-white rounded text-xs hover:opacity-80">
                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"></path>
                        </svg>
                        ${message.file_name}
                    </a>
                </div>
            ` : ''}
        </div>
    `;
    
    chatMessages.appendChild(messageDiv);
    scrollToBottom();
    
    // Update lastMessageId for polling
    lastMessageId = message.id;
}

// Close chat function
function closeChat(chatId) {
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

// Real-time updates via database polling (no external dependencies)
// Polling is handled by the unified chat widget component
</script>
@endsection
