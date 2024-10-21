<div id="main-content" class="relative z-10 flex flex-col items-center justify-center min-h-screen p-4">
    <div class="flex h-full flex-col items-center justify-center text-zinc-200">
        <div class="h-full w-full lg:py-[18px]">
            <div class="m-auto text-base px-3 md:px-4 w-full md:px-5 lg:px-4 xl:px-5 h-full">
                <div id="main-content-inner" class="mx-auto flex h-full w-full flex-col text-base justify-between md:max-w-3xl">
                    <div id="message-list" class="flex-grow overflow-y-auto space-y-4">
                        @if(isset($thread) && $thread->messages->isNotEmpty())
                            @include('chat.show', ['messages' => $thread->messages])
                        @else
                            <div class="mb-7 text-center">
                                <div class="select-none pointer-events-none inline-flex justify-center text-2xl font-semibold leading-9">
                                    <h1>How can we help?</h1>
                                </div>
                            </div>
                        @endif
                    </div>
                    <div class="mt-4">
                        @include('dashboard.message-form', ['thread' => $thread ?? null])
                        @include('dashboard.terms-privacy')
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const messageList = document.getElementById('message-list');

        // Custom event listener for new messages
        document.addEventListener('newMessage', function(e) {
            const messageHtml = e.detail.html;
            const tempDiv = document.createElement('div');
            tempDiv.innerHTML = messageHtml;
            const newMessage = tempDiv.firstElementChild;
            messageList.insertBefore(newMessage, messageList.firstChild);
            console.log('New message added:', messageHtml); // Add this line for debugging
        });
    });
</script>
@endpush