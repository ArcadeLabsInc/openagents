<div class="flex h-screen w-full overflow-hidden">
    <div class="w-full h-screen flex flex-col">
        <div class="fixed top-[60px] w-screen left-[0px] right-0 h-[40px] bg-gradient-to-b from-black to-transparent z-[9]"></div>
        
        @if(!$messages)
            <!-- big thing in the center -->
            <div class="flex items-center justify-center h-full">
                <div class="flex flex-col items-center">
                    <x-application-logo class="w-24 h-24"/>
                    <div class="mt-4 text-white text-center">
                        <h1>OpenAgents Chat</h1>
                        <p class="text-gray">Select an agent to start a chat</p>
                    </div>


                    <div class="mt-3 flex justify-center">
                        <div x-data="{
                                open: false,
                                toggle() {
                                    if (this.open) {
                                        return this.close()
                                    }

                                    this.$refs.button.focus()

                                    this.open = true
                                },
                                close(focusAfter) {
                                    if (! this.open) return

                                    this.open = false

                                    focusAfter && focusAfter.focus()
                                }
                            }" x-on:keydown.escape.prevent.stop="close($refs.button)"
                             x-on:focusin.window="! $refs.panel.contains($event.target) && close()"
                             x-id="['dropdown-button']" class="relative">
                            <!-- Button -->
                            <button x-ref="button" x-on:click="toggle()" :aria-expanded="open"
                                    :aria-controls="$id('dropdown-button')" type="button"
                                    class="flex items-center gap-2 bg-offblack px-5 py-2.5 rounded-md shadow">
                                Junior Developer

                                <!-- Heroicon: chevron-down -->
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400"
                                     viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd"
                                          d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                          clip-rule="evenodd"/>
                                </svg>
                            </button>

                            <!-- Panel -->
                            <div x-ref="panel" x-show="open" x-transition.origin.top.left
                                 x-on:click.outside="close($refs.button)" :id="$id('dropdown-button')"
                                 style="display: none;"
                                 class="absolute left-0 mt-2 w-44 rounded-md bg-offblack shadow-md">
                                <a href="#"
                                   class="flex items-center gap-2 w-full first-of-type:rounded-t-md last-of-type:rounded-b-md px-4 py-2.5 text-left text-sm hover:bg-darkgray disabled:text-gray-500">
                                    Junior Developer
                                </a>


                                <a href="#"
                                   class="flex items-center gap-2 w-full first-of-type:rounded-t-md last-of-type:rounded-b-md px-4 py-2.5 text-left text-sm hover:bg-darkgray disabled:text-gray-500">
                                    Bitcoin 101 Instructor
                                </a>

                                <a href="#"
                                   class="flex items-center gap-2 w-full first-of-type:rounded-t-md last-of-type:rounded-b-md px-4 py-2.5 text-left text-sm hover:bg-darkgray disabled:text-gray-500">
                                    VC Associate
                                </a>

                                <a href="#"
                                   class="flex items-center gap-2 w-full first-of-type:rounded-t-md last-of-type:rounded-b-md px-4 py-2.5 text-left text-sm hover:bg-darkgray disabled:text-gray-500">
                                    Your Waifu
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <div id="chatbox-container" class="mt-[70px] mb-[5px] flex-1 overflow-auto bg-gray-900 text-white">
            @foreach($messages as $message)
                @php
                    $author = $message['agent_id'] ? 'Agent Builder' : 'User';
                @endphp
                <x-message :author="$author" :message="$message['body']"/>
            @endforeach

            @if($pending)
                <x-messagestreaming :author="$agent->name ?? 'Agent'"/>
            @endif
        </div>

        <div class="fixed bottom-0 left-[0px] right-0 h-[80px] px-4 py-3 flex items-center z-10">
            <div class="fixed bottom-0 w-screen left-[0px] right-0 h-[70px] bg-black z-5"></div>
            <div
                    class="fixed bottom-[70px] w-screen left-[0px] right-0 h-[40px] bg-gradient-to-t from-black to-transparent z-5">
            </div>
            <div
                    class="w-full pt-2 md:pt-0 dark:border-white/20 md:border-transparent md:dark:border-transparent md:w-[calc(100%-.5rem)]">
                <form wire:submit.prevent="sendMessage"
                      class="stretch mx-2 flex flex-row gap-3 last:mb-2 md:mx-4 md:last:mb-6 lg:mx-auto lg:max-w-2xl xl:max-w-3xl">
                    <div class="relative flex h-full flex-1 items-stretch md:flex-col">
                        <div class="flex w-full items-center text-white">
                            <div x-data x-init="$refs.answer.focus()"
                                 class="overflow-hidden [&amp;:has(textarea:focus)]:border-gray [&amp;:has(textarea:focus)]:shadow-[0_2px_6px_rgba(0,0,0,.05)] flex flex-col w-full dark:border-gray flex-grow relative border border-gray dark:text-white rounded-[6px]">
                                <textarea x-ref="answer" id="message-input" name="message_input"
                                          wire:model="message_input"
                                          autofocus
                                          onkeydown="if(event.keyCode == 13 && !event.shiftKey) { event.preventDefault(); document.getElementById('send-message').click(); }"
                                          tabindex="0" rows="1" placeholder="{{ 'Message ' . $agent->name . '...' }}"
                                          class=" outline-none m-0 w-full resize-none border-0 bg-transparent
                                          focus:ring-0 focus-visible:ring-0 dark:bg-transparent max-h-25 py-[10px] pr-10
                                          md:py-3.5 md:pr-12 placeholder-white/50 pl-10 md:pl-[22px]"
                                          style="height: 52px; overflow-y: hidden;"></textarea>

                                <button id="send-message" class="absolute bottom-1.5 right-2 rounded-lg border border-black bg-black p-0.5
                                    text-white transition-colors enabled:bg-black disabled:text-gray-400
                                    disabled:opacity-25 dark:border-white dark:bg-white dark:hover:bg-white md:bottom-3
                                    md:right-3">
                                    <span class="" data-state="closed">
                                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" class="text-gray">
                                            <path d="M7 11L12 6L17 11M12 18V7" stroke="currentColor" stroke-width="2"
                                                  stroke-linecap="round" stroke-linejoin="round"></path>
                                        </svg>
                                    </span>
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        window.addEventListener('scrollToBottomAgain', event => {
            console.log("scrolling")
            // Scroll chatbox-container to the bottom
            setTimeout(() => {
                let chatboxContainer = document.querySelector('#chatbox-container');
                // chatboxContainer.scrollTop = chatboxContainer.scrollHeight;
                chatboxContainer.scrollTo({
                    top: chatboxContainer.scrollHeight,
                    behavior: 'smooth'
                });
            }, 200)

        })
    </script>
</div>
