<div class="w-full text-lightgray">
    <div class="px-4 py-2 justify-center text-base md:gap-6 m-auto">
        <div
                class="flex flex-1 text-base mx-auto gap-3 md:px-5 lg:px-1 xl:px-5 md:max-w-3xl lg:max-w-[800px]">
            <div class="flex-shrink-0 flex flex-col relative items-end">
                @if ($author === 'You')
                    <div class="m-[2px] h-[36px] w-[36px] items-center justify-center bg-darkgray rounded-full">
                        <img src="https://pbs.twimg.com/profile_images/1607882836740120576/3Tg1mTYJ_400x400.jpg"
                             alt="{{ $author }}" class="h-[36px] w-[36px] rounded-full"/>
                    </div>
                @else
                    <div class="m-[2px] w-[36px] p-2 border border-darkgray rounded">
                        <x-icon name="code" class=""/>
                    </div>
                @endif
            </div>
            <div class="relative flex w-full flex-col">
                <span class="mb-1 font-semibold select-none text-white">{{ $author }}</span>
                <div class="flex-col gap-1 md:gap-3">
                    <div class="-mt-4 flex flex-grow flex-col max-w-[936px]">
                        @if($author !== 'You')
                            <x-markdown class="text-md">{!! $message !!}</x-markdown>
                        @else
                            <x-markdown class="text-md">{{ $message }}</x-markdown>
                        @endif
                    </div>
                    <div class="flex justify-start gap-3 empty:hidden">
                        <div
                                class="text-gray flex self-end lg:self-center justify-center lg:justify-start mt-0 -ml-1 h-7 visible">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
