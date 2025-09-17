<?php

namespace App\Broadcasting\Drivers;

use Illuminate\Broadcasting\Broadcasters\Broadcaster;
use Illuminate\Broadcasting\Broadcasters\UsePusherChannelConventions;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Broadcasting\BroadcastManager;

class PollingBroadcaster extends Broadcaster
{
    use UsePusherChannelConventions;

    /**
     * Broadcast the given event.
     */
    public function broadcast(array $channels, $event, array $payload = [])
    {
        foreach ($channels as $channel) {
            $this->broadcastToChannel($channel, $event, $payload);
        }
    }

    /**
     * Broadcast to a specific channel.
     */
    protected function broadcastToChannel($channel, $event, array $payload = [])
    {
        try {
            // Store the event in cache for polling clients to pick up
            $cacheKey = "broadcast:{$channel}:{$event}:" . time();
            $data = [
                'channel' => $channel,
                'event' => $event,
                'data' => $payload,
                'timestamp' => time(),
            ];
            
            // Store for 60 seconds (clients should poll within this time)
            Cache::put($cacheKey, $data, 60);
            
            Log::info('Broadcast event stored', [
                'channel' => $channel,
                'event' => $event,
                'cache_key' => $cacheKey
            ]);
            
        } catch (\Exception $e) {
            Log::error('Failed to broadcast event', [
                'channel' => $channel,
                'event' => $event,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Get the channel name from the given channel.
     */
    public function getChannelName($channel)
    {
        return $channel;
    }
}
