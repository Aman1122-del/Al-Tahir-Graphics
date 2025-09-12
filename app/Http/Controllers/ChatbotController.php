<?php

namespace App\Http\Controllers;

use App\Models\ChatAttachment;
use App\Models\ChatMessage;
use App\Models\ChatSession;
use App\Models\ChatSetting;
use App\Models\ChatTicket;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ChatbotController extends Controller
{
    public function settings(): array
    {
        $settings = ChatSetting::query()->first();
        if (!$settings) {
            $settings = ChatSetting::create([
                'force_bot_only' => false,
                'force_live_only' => false,
                'business_hours' => [
                    ['day' => 'mon', 'start' => '09:00', 'end' => '18:00'],
                    ['day' => 'tue', 'start' => '09:00', 'end' => '18:00'],
                    ['day' => 'wed', 'start' => '09:00', 'end' => '18:00'],
                    ['day' => 'thu', 'start' => '09:00', 'end' => '18:00'],
                    ['day' => 'fri', 'start' => '09:00', 'end' => '18:00'],
                ],
            ]);
        }
        return [
            'force_bot_only' => $settings->force_bot_only,
            'force_live_only' => $settings->force_live_only,
            'business_hours' => $settings->business_hours,
            'is_open_now' => $this->isOpenNow($settings),
        ];
    }

    protected function isOpenNow(ChatSetting $settings): bool
    {
        if ($settings->force_bot_only) {
            return false;
        }
        if ($settings->force_live_only) {
            return true;
        }
        $hours = $settings->business_hours ?? [];
        $now = now();
        $day = strtolower($now->format('D')); // mon, tue, ...
        foreach ($hours as $slot) {
            if (($slot['day'] ?? '') === $day) {
                $start = $slot['start'] ?? '00:00';
                $end = $slot['end'] ?? '23:59';
                $startTime = $now->copy()->setTimeFromTimeString($start);
                $endTime = $now->copy()->setTimeFromTimeString($end);
                if ($now->between($startTime, $endTime)) {
                    return true;
                }
            }
        }
        return false;
    }

    public function start(Request $request)
    {
        $session = ChatSession::create([ 'status' => 'bot' ]);
        $this->botMessage($session, "Hi! How can I help you today?", [
            'menu' => [
                ['key' => 'order-status', 'label' => 'Order status'],
                ['key' => 'quote', 'label' => 'Request a quote'],
                ['key' => 'pricing', 'label' => 'Pricing info'],
                ['key' => 'upload', 'label' => 'Upload files'],
                ['key' => 'message', 'label' => 'Leave a message'],
            ]
        ]);
        return response()->json(['session_id' => $session->id]);
    }

    public function menu(Request $request, ChatSession $session)
    {
        $validated = $request->validate([
            'choice' => 'required|string|in:order-status,quote,pricing,upload,message',
        ]);
        $session->update(['topic' => $validated['choice']]);
        $this->userMessage($session, 'Menu choice: ' . $validated['choice']);

        switch ($validated['choice']) {
            case 'order-status':
                $this->botMessage($session, 'Please enter your order number.');
                break;
            case 'quote':
                $this->botMessage($session, 'Please describe your requirements. We will prepare a quote.');
                break;
            case 'pricing':
                $this->botMessage($session, 'Pricing: Wedding Cards from PKR 3,999; Visiting Cards from PKR 999; Flyers from PKR 1,499; Banners from PKR 2,999; Brochures from PKR 2,499; Posters from PKR 1,999.');
                break;
            case 'upload':
                $this->botMessage($session, 'You can upload files now (PDF, images up to 20MB).');
                break;
            case 'message':
                $this->botMessage($session, 'Please type your message and contact info.');
                break;
        }

        return response()->json(['ok' => true]);
    }

    public function message(Request $request, ChatSession $session)
    {
        $text = (string) $request->input('message', '');
        $this->userMessage($session, $text);
        return response()->json(['ok' => true]);
    }

    public function contact(Request $request, ChatSession $session)
    {
        $validated = $request->validate([
            'name' => 'nullable|string|max:120',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:50',
        ]);
        $session->update([
            'user_name' => $validated['name'] ?? null,
            'user_email' => $validated['email'] ?? null,
            'user_phone' => $validated['phone'] ?? null,
        ]);
        $this->userMessage($session, 'Provided contact details.', ['contact' => $validated]);
        return response()->json(['ok' => true]);
    }

    public function upload(Request $request, ChatSession $session)
    {
        $request->validate([
            'file' => 'required|file|max:20480',
        ]);
        $file = $request->file('file');
        $path = $file->store('chat-uploads', 'public');

        $message = ChatMessage::create([
            'chat_session_id' => $session->id,
            'sender' => 'user',
            'message' => 'Uploaded file: ' . $file->getClientOriginalName(),
        ]);

        ChatAttachment::create([
            'chat_session_id' => $session->id,
            'chat_message_id' => $message->id,
            'original_name' => $file->getClientOriginalName(),
            'path' => $path,
            'mime_type' => $file->getClientMimeType(),
            'size_bytes' => $file->getSize(),
        ]);

        return response()->json(['ok' => true, 'path' => asset('storage/' . $path)]);
    }

    public function ticket(Request $request, ChatSession $session)
    {
        if ($session->ticket) {
            return response()->json(['ticket' => $session->ticket]);
        }
        $subject = $request->input('subject') ?: ('Chat with ' . ($session->user_name ?: 'guest'));
        $ticket = ChatTicket::create([
            'chat_session_id' => $session->id,
            'ticket_number' => strtoupper(Str::random(10)),
            'status' => 'open',
            'subject' => $subject,
        ]);
        $this->botMessage($session, 'Your ticket has been created: ' . $ticket->ticket_number);
        return response()->json(['ticket' => $ticket]);
    }

    public function offerAgent(Request $request, ChatSession $session)
    {
        if ($session->status === 'live') {
            return response()->json(['status' => 'already-live']);
        }
        $session->update(['agent_offered' => true, 'agent_offer_at' => now(), 'status' => 'queued']);
        $this->botMessage($session, 'An agent is available now. Would you like to connect?');
        return response()->json(['ok' => true]);
    }

    public function acceptAgent(Request $request, ChatSession $session)
    {
        $session->update(['status' => 'live']);
        $this->botMessage($session, 'Connecting you to a live agent...');
        return response()->json(['ok' => true]);
    }

    public function poll(Request $request, ChatSession $session)
    {
        $sinceId = (int) $request->query('since_id', 0);
        $messages = ChatMessage::query()
            ->where('chat_session_id', $session->id)
            ->when($sinceId > 0, fn($q) => $q->where('id', '>', $sinceId))
            ->orderBy('id')
            ->limit(100)
            ->get(['id', 'sender', 'message', 'metadata', 'created_at']);

        return response()->json([
            'session' => [
                'status' => $session->status,
                'agent_offered' => (bool) $session->agent_offered,
            ],
            'messages' => $messages,
        ]);
    }

    protected function botMessage(ChatSession $session, string $text, array $metadata = []): void
    {
        ChatMessage::create([
            'chat_session_id' => $session->id,
            'sender' => 'bot',
            'message' => $text,
            'metadata' => $metadata ?: null,
        ]);
    }

    protected function userMessage(ChatSession $session, string $text, array $metadata = []): void
    {
        ChatMessage::create([
            'chat_session_id' => $session->id,
            'sender' => 'user',
            'message' => $text,
            'metadata' => $metadata ?: null,
        ]);
    }
}


