<?php

namespace App\Jobs;

use App\Models\UnifiedChatMessage;
use App\Models\Setting;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class SendChatNotificationEmail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $message;

    /**
     * Create a new job instance.
     */
    public function __construct(UnifiedChatMessage $message)
    {
        $this->message = $message;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            // Get email settings
            $settings = Setting::first();
            
            if (!$settings || !$settings->chat_email_notifications || !$settings->chat_admin_email) {
                Log::info('Chat email notifications disabled or admin email not configured');
                return;
            }

            // Only send notifications for user messages (not admin replies)
            if ($this->message->sender_id && $this->message->sender) {
                // This is a registered user message
                $senderName = $this->message->sender->name;
                $senderEmail = $this->message->sender->email;
            } else {
                // This is a guest message
                $senderName = $this->message->sender_name ?? 'Guest';
                $senderEmail = $this->message->sender_email ?? 'Unknown';
            }

            // Prepare email data
            $emailData = [
                'message' => $this->message,
                'senderName' => $senderName,
                'senderEmail' => $senderEmail,
                'chatTitle' => $this->message->chat->title ?? 'Support Chat',
                'chatId' => $this->message->chat->id,
            ];

            // Send email using configured SMTP settings
            if ($settings->chat_smtp_host) {
                config([
                    'mail.mailers.smtp.host' => $settings->chat_smtp_host,
                    'mail.mailers.smtp.port' => $settings->chat_smtp_port ?? 587,
                    'mail.mailers.smtp.username' => $settings->chat_smtp_username,
                    'mail.mailers.smtp.password' => $settings->chat_smtp_password,
                    'mail.mailers.smtp.encryption' => $settings->chat_smtp_encryption ? 'tls' : null,
                ]);
            }

            Mail::to($settings->chat_admin_email)
                ->send(new \App\Mail\ChatNotificationMail($emailData));

            Log::info('Chat notification email sent successfully', [
                'message_id' => $this->message->id,
                'sender' => $senderEmail,
                'admin_email' => $settings->chat_admin_email,
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to send chat notification email', [
                'message_id' => $this->message->id,
                'error' => $e->getMessage(),
            ]);
            
            // Don't fail the job, just log the error
            $this->fail($e);
        }
    }

    /**
     * Handle a job failure.
     */
    public function failed(\Throwable $exception): void
    {
        Log::error('Chat notification email job failed', [
            'message_id' => $this->message->id,
            'error' => $exception->getMessage(),
        ]);
    }
}