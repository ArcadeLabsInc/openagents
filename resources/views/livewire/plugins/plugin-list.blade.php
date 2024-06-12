<div>
    {{-- Knowing others is intelligence; knowing yourself is true wisdom. --}}


    <div class=" mx-auto mt-14 mb-6 px-4 pt-4 md:px-8">
        <div class="items-center justify-between gap-x-1 py-4 border-b border-offblack md:flex">
            <div class="md:max-w-md lg:max-w-lg">
                <h3 class="text-gray-800 text-2xl font-bold">
                    Plugin Registry
                </h3>
            </div>
            <div class="mt-6 sm:mt-0 w-auto">
                <div class="flex gap-2 rounded-md shadow-sm  w-auto lg:w-[350px]">
                    <div class="relative flex-grow focus-within:z-10">
                        <div class="text-darkgray pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                            <svg class="h-5 w-5 text-gray-400" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                <path fill-rule="evenodd"
                                    d="M9 3.5a5.5 5.5 0 100 11 5.5 5.5 0 000-11zM2 9a7 7 0 1112.452 4.391l3.328 3.329a.75.75 0 11-1.06 1.06l-3.329-3.328A7 7 0 012 9z"
                                    clip-rule="evenodd"></path>
                            </svg>
                        </div>
                        <input type="search" name="search" id="search"
                            class="bg-black w-full text-darkgray rounded border-0 py-1.5 pl-10 text-sm leading-6 text-gray-900 ring-1 ring-darkgray ring-inset ring-gray-300 placeholder:text-darkgray focus:ring-2 focus:ring-inset focus:ring-gray sm:block"
                            placeholder="Search plugins" wire:model.live='search' />
                    </div>
                    <a href="{{ route('plugins.edit') }}" type="button" wire:navigate
                        class="relative sm:-ml-px inline-flex items-center gap-x-1 sm:gap-x-1.5 rounded px-3 py-2 text-xs sm:text-sm sm:font-semibold text-gray-900 ring-1 ring-inset ring-gray/50 hover:bg-gray/50">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                            stroke="currentColor" class="w-5 h-5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m6-6H6" />
                        </svg>
                        Create
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="grid gap-2 p-8  md:gap-2 xl:gap-4 md:grid-cols-2 xl:grid-cols-4">

        @forelse ($this->plugins as $plugin)
            @if ($plugin->suspended && !$plugin->isEditableBy(auth()->user()))
                @continue
            @endif

            <x-pane title=" {{ $plugin->name }}" subtitle="by {{ $plugin->user->name }}"
                class="h-64 overflow-auto flex flex-col justify-between">
                <div class="items-center gap-1.5 pr-2  group-hover:flex">
                    @auth
                        @if ($plugin->isEditableBy(auth()->user()))
                            <div x-data="{ isOpen: false }" class="relative flex-1 text-right">
                                <button @click="isOpen = !isOpen" class="p-1.5 rounded-md text-gray hover:bg-[#262626]">
                                    <x-icon.dots role='button' class="w-4 h-4"></x-icon.dots>
                                </button>
                                <div x-show="isOpen" @click.away="isOpen = false"
                                    class="absolute z-10 top-12 right-0 w-64 rounded-lg bg-black border border-gray shadow-md  text-sm text-gray">
                                    <div class="p-2 text-left">
                                        <a href="{{ route('plugins.edit', ['plugin' => $plugin]) }}" wire:navigate
                                            class="block w-full p-2 text-left rounded-md hover:text-white  hover:bg-[#262626] duration-150"
                                            rel="nofollow">Edit</a>
                                        <a role="button"
                                            x-on:click="Livewire.dispatch('openModal', { component: 'plugins.modals.delete', arguments: { plugin: {{ $plugin->id }} } })"
                                            class="block w-full p-2 text-left rounded-md text-red hover:bg-[#262626] duration-150"
                                            rel="nofollow">Delete</a>
                                    </div>
                                </div>
                            </div>
                        @endif
                    @endauth
                </div>
                <p class="text-sm text-gray-400 mb-auto">
                    {{ $plugin->description }}
                </p>

                @if ($plugin->suspended && $plugin->isEditableBy(auth()->user()))
                    <div class="mt-5 w-full">
                        <p class="my-2 text-red text-sm text-center font-bold">
                            {{ $plugin->suspended }}
                        </p>
                    </div>
                @elseif ($plugin->pending_revision && $plugin->isEditableBy(auth()->user()))
                    <div class="mt-5 w-full">
                        <p class="my-2 text-red text-sm text-center font-bold">
                            {{ $plugin->pending_revision_reason }}
                        </p>
                    </div>
                @endif

                <div class="mt-5 w-full flex justify-center items-center gap-4">

                    @if ($plugin->web)
                        <a href="{{ $plugin->web }}" target="_blank" title="Visit website">
                            <x-icon.link class="h-4 w-4 text-white" style="stroke: white;" />
                        </a>
                    @endif
                    @if ($plugin->tos)
                        <a href="{{ $plugin->tos }}" target="_blank" title="Terms of Service">
                            <x-icon.link class="h-4 w-4 text-white" style="stroke: white;" />
                        </a>
                    @endif
                    @if ($plugin->privacy)
                        <a href="{{ $plugin->privacy }}" target="_blank" title="Privacy Policy">
                            <x-icon.link class="h-4 w-4 text-white" style="stroke: white;" />
                        </a>
                    @endif
                </div>





            </x-pane>

        @empty

            <div class="col-span-full my-auto mx-auto flex">
                {{-- <img src="{{ url('/images/no-image.jpg') }}" alt="No plugin available"  class="h-64 w-64 flex-none rounded-lg bg-gray-800 object-cover"> --}}

                <div class="bg-black w-full  border-1 border-offblack rounded p-3 sm:p-10 my-auto mx-auto">
                    <div class="col-span-full flex justify-center items-center  gap-x-8 my-auto text-center">
                        <h2> No plugins available.</h2>
                    </div>

                    <div class="col-span-full text-center  justify-center items-center  gap-x-8 my-auto">
                        <p class="mt-2 text-lg leading-8   text-white font-bold block">
                            No items were found at the moment, Please check back later.
                        </p>
                    </div>
                </div>
            </div>
        @endforelse
    </div>

</div>
