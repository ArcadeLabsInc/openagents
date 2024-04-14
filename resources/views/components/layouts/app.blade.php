<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'OpenAgents' }}</title>
    <script defer src="https://unpkg.com/@alpinejs/ui@3.13.8-beta.0/dist/cdn.min.js"></script>
    <script defer src="https://unpkg.com/@alpinejs/focus@3.13.8/dist/cdn.min.js"></script>
    @stack('scripts')
    @include('partials.vite')
    @include('partials.analytics')
    <x-social-tags/>

    <link rel="stylesheet" href="{{asset('vendor/swal-dark.min.css')}}">
</head>

<body class="h-screen bg-black antialiased" x-cloak
      x-data="{  sidebarOpen: window.innerWidth > 768, collapsed: false }"
      x-on:message-created.window="addCopyButtons()">

<div class="relative z-0 flex h-full w-full overflow-hidden min-h-screen">
    <button class="z-[9001] absolute top-0 left-0 cursor-pointer h-[28px] w-[28px] m-4 mt-[14px] mr-12"
            @click="sidebarOpen = !sidebarOpen">
        <x-icon.menu/>
    </button>
    <div class="flex-shrink-0 overflow-x-hidden sidebar"
         x-bind:class="{
            'sidebar-open': sidebarOpen,
            'sidebar-closed': !sidebarOpen
         }"
    >
        <div class="relative h-full w-[260px]">
            <div class="flex h-full min-h-0 flex-col">
                <div class="relative h-full w-full flex-1 items-start">
                    <div class="flex h-full w-full flex-col px-1 pb-3.5">
                        <div class="flex-col flex-1 transition-opacity duration-500 overflow-y-auto">
                            <livewire:layouts.sidebar-content/>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="relative flex h-full max-w-full flex-1 flex-col overflow-hidden hmmm"
         x-bind:style="`margin-left: ${sidebarOpen ? '0' : (window.innerWidth <= 768 ? '0' : '50px')}`"
    >
        <main class="relative h-full w-full flex-1 overflow-auto transition-width">
            {{$slot}}
        </main>
    </div>
</div>

@livewire('wire-elements-modal')
<x-livewire-alert::scripts/>

<script>
    function addCopyButtons(codeBlocks) {
        console.log('addCopyButtons() called');

        if (codeBlocks && codeBlocks.length) {
            console.log('Number of code blocks:', codeBlocks.length);

            codeBlocks.forEach(function (codeBlock, blockIndex) {
                console.log(`Processing code block ${blockIndex + 1}`);

                // Remove any existing "Copy" buttons
                const existingCopyButton = codeBlock.querySelector('.copy-button');
                if (existingCopyButton) {
                    console.log('Removing existing "Copy" button');
                    existingCopyButton.remove();
                }

                // Add a new "Copy" button
                const copyButton = document.createElement('button');
                copyButton.innerText = 'Copy';
                copyButton.classList.add('copy-button');
                copyButton.addEventListener('click', function () {
                    const code = codeBlock.querySelector('code').innerText;
                    navigator.clipboard.writeText(code).then(function () {
                        copyButton.innerText = 'Copied!';
                        setTimeout(function () {
                            copyButton.innerText = 'Copy';
                        }, 2000);
                    }, function (err) {
                        console.error('Failed to copy: ', err);
                    });
                });

                codeBlock.appendChild(copyButton);
                console.log('Added new "Copy" button');
            });
        }
    }

    function observeCodeBlocks() {
        const observer = new MutationObserver(function (mutations) {
            mutations.forEach(function (mutation) {
                if (mutation.type === 'childList') {
                    const addedNodes = mutation.addedNodes;
                    addedNodes.forEach(function (node) {
                        if (node.nodeType === Node.ELEMENT_NODE && node.matches('.markdown-content pre.shiki')) {
                            addCopyButtons([node]);
                        }
                    });
                }
            });
        });

        const observerOptions = {
            childList: true,
            subtree: true
        };

        const mainElement = document.querySelector('main');
        observer.observe(mainElement, observerOptions);
    }

    document.addEventListener('DOMContentLoaded', function () {
        console.log('DOMContentLoaded event triggered');
        const codeBlocks = document.querySelectorAll('.markdown-content pre.shiki');
        addCopyButtons(codeBlocks);
        observeCodeBlocks();
    });

    document.addEventListener('message-created', function () {
        console.log('message-created event triggered');
        const codeBlocks = document.querySelectorAll('.markdown-content pre.shiki');
        addCopyButtons(codeBlocks);
    });
</script>

</body>

</html>
