<?php
namespace App\Events\Forum;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class UserTyping implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public int    $threadId,
        public int    $userId,
        public string $userName,
        public bool   $isTyping
    ) {}

    public function broadcastOn(): array
    {
        return [new PresenceChannel('forum.thread.' . $this->threadId)];
    }

    public function broadcastAs(): string { return 'user.typing'; }

    public function broadcastWith(): array
    {
        return [
            'user_id'   => $this->userId,
            'user_name' => $this->userName,
            'is_typing' => $this->isTyping,
        ];
    }
}