<x-layout>
    <div class="h-full w-full text-foreground flex flex-col">
        <div class="flex-1 overflow-hidden flex flex-col">
            <main class="flex-1 overflow-y-auto">
                <div class="max-w-4xl mx-auto p-4 pt-16">
                    <h1 class="text-2xl font-bold mb-4">Chat Thread: {{ $thread->title }}</h1>
                    @if (count($thread->messages) > 0)
                        @foreach ($thread->messages as $message)
                            <x-chat.message :message="$message" />
                        @endforeach
                    @endif
                </div>
            </main>
        </div>

        <div class="flex-shrink-0 w-full">
            <div class="max-w-4xl mx-auto px-4 mb-2">
                <div class="w-full">
                    <form class="w-full" id="message-form" action="{{ route('messages.store', ['thread' => $thread->id]) }}" method="POST">
                        @csrf
                        <div class="flex w-full flex-col gap-1.5 rounded-[30px] p-1 transition-colors bg-zinc-900">
                            <div class="flex items-end gap-1.5 pl-4 py-0.5 md:gap-2">
                                <div class="flex min-w-0 flex-1 flex-col">
                                    <textarea
                                        name="content"
                                        id="message-textarea"
                                        class="min-h-[46px] max-h-[300px] overflow-y-auto resize-none flex w-full rounded-md bg-transparent px-1 py-[0.65rem] pr-10 text-[16px] placeholder:text-[#777A81] focus-visible:outline-none focus-visible:ring-0 border-none"
                                        placeholder="Message OpenAgents"
                                        rows="1"
                                        autofocus
                                        required
                                    ></textarea>
                                </div>
                                <div class="mb-1 me-1">
                                    <button type="submit" aria-label="Send prompt" data-testid="send-button" class="flex h-8 w-8 items-center justify-center rounded-full bg-zinc-700 text-zinc-200 transition-colors hover:bg-zinc-600 focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-zinc-500 disabled:bg-zinc-800 disabled:text-zinc-500 disabled:hover:bg-zinc-800" id="send-message" dusk="send-message">
                                        <svg width="32" height="32" viewBox="0 0 32 32" fill="none" xmlns="http://www.w3.org/2000/svg" class="icon-2xl">
                                            <path fill-rule="evenodd" clip-rule="evenodd" d="M15.1918 8.90615C15.6381 8.45983 16.3618 8.45983 16.8081 8.90615L21.9509 14.049C22.3972 14.4953 22.3972 15.2189 21.9509 15.6652C21.5046 16.1116 20.781 16.1116 20.3347 15.6652L17.1428 12.4734V22.2857C17.1428 22.9169 16.6311 23.4286 15.9999 23.4286C15.3688 23.4286 14.8571 22.9169 14.8571 22.2857V12.4734L11.6652 15.6652C11.2189 16.1116 10.4953 16.1116 10.049 15.6652C9.60265 15.2189 9.60265 14.4953 10.049 14.049L15.1918 8.90615Z" fill="currentColor"></path>
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            <div class="pb-2 text-center text-xs text-zinc-500">
                @if (auth()->check() && auth()->user()->currentTeam)
                    Messages visible to all members of {{ auth()->user()->currentTeam->name }}
                @else
                    Messages are visible only to you
                @endif
            </div>
        </div>
    </div>
</x-layout>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const textarea = document.getElementById('message-textarea');
    const form = document.getElementById('message-form');

    textarea.addEventListener('keydown', function(e) {
        if (e.key === 'Enter' && !e.shiftKey) {
            if (e.metaKey || e.ctrlKey) {
                // Insert a newline
                const start = this.selectionStart;
                const end = this.selectionEnd;
                const value = this.value;
                this.value = value.slice(0, start) + "\n" + value.slice(end);
                this.selectionStart = this.selectionEnd = start + 1;
            } else {
                // Submit the form
                e.preventDefault();
                form.submit();
            }
        }
    });

    textarea.addEventListener('input', function() {
        this.style.height = 'auto';
        this.style.height = this.scrollHeight + 'px';
        this.closest('.flex').style.height = 'auto';
        this.closest('.flex').style.height = this.scrollHeight + 'px';
    });
});
</script>
@endpush