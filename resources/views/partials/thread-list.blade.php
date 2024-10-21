<div id="thread-list" class="space-y-2">
    @forelse($threads as $thread)
    <a href="{{ route('chat.show', $thread->id) }}"
        class="cursor-pointer block p-2 hover:bg-sidebar-accent rounded-md transition-colors duration-200"
        hx-get="{{ route('chat.messages', $thread->id) }}"
        hx-target="#message-list"
        hx-swap="innerHTML"
        hx-push-url="false">
        <div class="text-sm font-medium text-sidebar-foreground">{{ $thread->title }}</div>
        <div class="text-xs text-sidebar-muted">{{ $thread->created_at->diffForHumans() }}</div>
    </a>
    @empty
    <p class="text-sidebar-muted text-sm">No chats yet. Start a new conversation!</p>
    @endforelse
</div>