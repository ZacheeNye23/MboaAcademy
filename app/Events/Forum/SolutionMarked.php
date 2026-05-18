<?php
namespace App\Events\Forum;

use App\Models\ForumReply;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class SolutionMarked implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public int $threadId,
        public int $replyId,
        public bool $isSolved
    ) {}

    public function broadcastOn(): array
    {
        return [new PresenceChannel('forum.thread.' . $this->threadId)];
    }

    public function broadcastAs(): string { return 'solution.marked'; }

    public function broadcastWith(): array
    {
        return [
            'reply_id'  => $this->replyId,
            'is_solved' => $this->isSolved,
        ];
    }
}