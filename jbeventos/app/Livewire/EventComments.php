<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithFileUploads;
use App\Models\Comment;
use Illuminate\Support\Facades\Auth;
use App\Models\CommentReaction;

class EventComments extends Component
{
    use WithFileUploads;

    public $event;
    public $commentText = '';
    public $media;
    public $replyTo = null;
    public $editingCommentId = null;
    public $comments = [];

    protected $rules = [
        'commentText' => 'required|string|max:1000',
        'media' => 'nullable|file|max:2048|mimes:jpg,jpeg,png,webp,mp4,pdf'
    ];

    public function mount($event)
    {
        $this->event = $event;
        $this->loadComments();
    }

    public function render()
    {
        return view('livewire.event-comments', [
            'comments' => $this->comments
        ]);
    }

    public function loadComments()
    {
        $this->comments = Comment::with(['user', 'replies.user', 'reactions'])
            ->withCount([
                'reactions as likes_count' => function ($q) {
                    $q->where('type', 'like');
                },
                'reactions as dislikes_count' => function ($q) {
                    $q->where('type', 'dislike');
                },
            ])
            ->where('event_id', $this->event->id)
            ->whereNull('parent_id')
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function addComment()
    {
        $this->validate();

        $mediaPath = $this->media ? $this->media->store('comments', 'public') : null;

        Comment::create([
            'comment' => $this->commentText,
            'user_id' => Auth::id(),
            'event_id' => $this->event->id,
            'parent_id' => $this->replyTo,
            'media_path' => $mediaPath,
        ]);

        $this->reset(['commentText', 'media', 'replyTo']);
        $this->loadComments();
    }

    public function setReply($commentId)
    {
        $this->replyTo = $commentId;
    }

    public function cancelReply()
    {
        $this->reset('replyTo');
    }

    public function editComment($id)
    {
        $comment = Comment::find($id);
        if ($comment && $comment->user_id === Auth::id()) {
            $this->editingCommentId = $id;
            $this->commentText = $comment->comment;
        }
    }

    public function updateComment()
    {
        $this->validate();
        $comment = Comment::find($this->editingCommentId);

        if ($comment && $comment->user_id === Auth::id()) {
            $comment->update([
                'comment' => $this->commentText,
                'edited_at' => now(),
            ]);
        }

        $this->reset(['commentText', 'editingCommentId']);
        $this->loadComments();
    }

    public function deleteComment($id)
    {
        $comment = Comment::find($id);
        if ($comment && $comment->user_id === Auth::id()) {
            $comment->delete();
        }
        $this->loadComments();
    }

    public function reactToComment($commentId, $type)
    {
        $userId = auth()->id();
        $reaction = CommentReaction::where('comment_id', $commentId)
            ->where('user_id', $userId)
            ->first();

        if ($reaction) {
            if ($reaction->type === $type) {
                $reaction->delete();
            } else {
                $reaction->update(['type' => $type]);
            }
        } else {
            CommentReaction::create([
                'user_id' => $userId,
                'comment_id' => $commentId,
                'type' => $type,
            ]);
        }

        $this->loadComments();
    }
}
