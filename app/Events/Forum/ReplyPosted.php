<?php
namespace App\Events\Forum;

use App\Models\ForumReply;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ReplyPosted implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public array $replyData;

    public function __construct(public ForumReply $reply)
    {
        // Charger les relations nécessaires
        $reply->load('author');

        // Préparer les données sérialisables (pas d'objet Eloquent brut)
        $this->replyData = [
            'id'          => $reply->id,
            'body'        => $reply->body,
            'parent_id'   => $reply->parent_id,
            'is_solution' => $reply->is_solution,
            'created_at'  => $reply->created_at->toIso8601String(),
            'created_ago' => $reply->created_at->diffForHumans(),
            'author' => [
                'id'         => $reply->author->id,
                'full_name'  => $reply->author->full_name,
                'initials'   => $reply->author->initials,
                'avatar_url' => $reply->author->avatar_url,
                'role'       => $reply->author->role,
            ],
        ];
    }

    // Canal de présence du thread (connait les membres connectés)
    public function broadcastOn(): array
    {
        return [
            new PresenceChannel('forum.thread.' . $this->reply->thread_id),
        ];
    }

    public function broadcastAs(): string
    {
        return 'reply.posted';
    }

    public function broadcastWith(): array
    {
        return ['reply' => $this->replyData];
    }
}