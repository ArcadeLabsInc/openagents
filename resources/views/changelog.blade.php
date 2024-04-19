<x-blog-layout>
    <div class="pt-4 pb-24 bg-gray-100 dark:bg-gray-900">
        <div class="flex flex-col items-center pt-6 sm:pt-0">
            <a href="/" wire:navigate class="mt-12">
                <x-icon.logo class="w-20 h-20 text-white"/>
            </a>

            <a href="/" wire:navigate>
                <h3 class="text-[16px] fixed top-[18px] left-[24px] text-gray"> &larr; Back to chat</h3>
            </a>

            <h1 class="mt-12 text-center">Changelog</h1>


            <div class="w-full sm:max-w-2xl mt-6 p-6 bg-black shadow-md overflow-hidden sm:rounded-lg prose prose-invert">
                <div class="mt-6 grid grid-cols-1 gap-10">
                    <x-pane title="April 19, 2024" borderColor="border-darkgray">
                        <x-changelog-item
                                code="https://github.com/OpenAgentsInc/openagents/pull/280"
                        >
                            Set last-used model when opening thread
                        </x-changelog-item>
                        <x-changelog-item
                                code="https://github.com/OpenAgentsInc/openagents/pull/276"
                                post="https://twitter.com/OpenAgentsInc/status/1781333474852688099"
                        >
                            Improve AI thread titles
                        </x-changelog-item>
                    </x-pane>

                    <x-pane title="April 18, 2024" borderColor="border-darkgray">
                        <x-changelog-item
                                code="https://github.com/OpenAgentsInc/openagents/pull/276"
                                post="https://twitter.com/OpenAgentsInc/status/1781028741109444719"
                        >
                            Added Llama 3 8B and 70B chat models
                        </x-changelog-item>
                        <x-changelog-item
                                code="https://github.com/OpenAgentsInc/openagents/pull/278"
                        >
                            Add AI titles to untitled threads
                        </x-changelog-item>
                    </x-pane>

                    <x-pane title="April 17, 2024" borderColor="border-darkgray">
                        <x-changelog-item
                                code="https://github.com/OpenAgentsInc/openagents/pull/273"
                                post="https://twitter.com/OpenAgentsInc/status/1780642250411679938"
                        >
                            Added plugin registry
                        </x-changelog-item>
                        <x-changelog-item
                                code="https://github.com/OpenAgentsInc/openagents/pull/274"
                                post="https://twitter.com/OpenAgentsInc/status/1780722536126255568"
                        >
                            Added chat with OA codebase via Greptile
                        </x-changelog-item>
                    </x-pane>

                    <x-pane title="April 16, 2024" borderColor="border-darkgray">
                        <x-changelog-item
                                code="https://github.com/OpenAgentsInc/openagents/pull/270"
                                post="https://twitter.com/OpenAgentsInc/status/1780347365049630907"
                        >
                            Added Nostr login
                        </x-changelog-item>

                        <x-changelog-item
                                code="https://github.com/OpenAgentsInc/openagents/pull/269"
                                post="https://twitter.com/OpenAgentsInc/status/1780264061277540522"
                        >
                            Added model images
                        </x-changelog-item>
                    </x-pane>

                    <x-pane title="April 15, 2024" borderColor="border-darkgray">
                        <x-changelog-item
                                code="https://github.com/OpenAgentsInc/openagents/pull/267"
                                post="https://twitter.com/OpenAgentsInc/status/1779907555977769160"
                        >
                            Added this changelog
                        </x-changelog-item>
                        <x-changelog-item
                                code="https://github.com/OpenAgentsInc/openagents/commit/f17ec9a31745009179d4dcb53e593a19d78c745e">
                            Fixed menu buttons
                        </x-changelog-item>
                        <x-changelog-item
                                code="https://github.com/OpenAgentsInc/openagents/pull/267/commits/0feb663b9fb9b24f98f33aebef5cf53ddc1ac0ef">
                            Fixed OpenAI model bug
                        </x-changelog-item>
                    </x-pane>

                    <x-pane title="April 12, 2024" borderColor="border-darkgray">
                        <x-changelog-item
                                code="https://github.com/OpenAgentsInc/openagents/pull/261"
                                post="https://twitter.com/OpenAgentsInc/status/1778822995261141143"
                        >
                            Added chat model Satoshi 7B
                        </x-changelog-item>
                    </x-pane>

                    <x-pane title="April 11, 2024" borderColor="border-darkgray">
                        <x-changelog-item
                                code="https://github.com/OpenAgentsInc/openagents/pull/260"
                                post="https://twitter.com/OpenAgentsInc/status/1778420801253101652"
                        >
                            Added Cohere chat models Command R and R+
                        </x-changelog-item>
                    </x-pane>

                    <x-pane title="April 10, 2024" borderColor="border-darkgray">
                        <x-changelog-item
                                code="https://github.com/OpenAgentsInc/openagents/pull/259"
                                post="https://twitter.com/OpenAgentsInc/status/1778150350383440316"
                        >
                            Added Perplexity Sonar online chat models
                        </x-changelog-item>
                    </x-pane>

                    <x-pane title="April 9, 2024" borderColor="border-darkgray">
                        <x-changelog-item
                                code="https://github.com/OpenAgentsInc/openagents/commit/76a0be7e3fc408726ec03226984ecab635338f10"
                                post="https://twitter.com/OpenAgentsInc/status/1777863950014570549"
                        >
                            Added "majorly improved" GPT-4 Turbo model
                        </x-changelog-item>
                    </x-pane>

                    <x-pane title="April 8, 2024" borderColor="border-darkgray">
                        <x-changelog-item
                                code="https://github.com/OpenAgentsInc/openagents/tree/e6ba003c4dfca4668a49527b1b268ea5d05b96ff"
                                post="https://twitter.com/OpenAgentsInc/status/1777496991099998302"
                        >
                            Launch new chat interface
                        </x-changelog-item>
                    </x-pane>


                </div>
                <p class="text-sm text-center text-gray">
                    See our <a href="https://github.com/OpenAgentsInc/openagents/commits/main/" target="_blank">GitHub
                        commit history</a> for the full list of changes.
                </p>
            </div>
        </div>
    </div>
</x-blog-layout>
