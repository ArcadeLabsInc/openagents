<div>
    {{-- Nothing in the world is as soft and yielding as water. --}}
    @livewire('navbar')
    <div class="flex items-center justify-end w-full h-screen">
        <div class="border border-[#3C3E42] bg-black rounded-lg p-6  max-w-lg h-auto mx-auto  shadow-lg transform transition-all duration-300">
            <!-- Modal Header -->

            <div class="">
                <h2 class="block text-xl text-center font-bold text-gray-800 dark:text-gray-200">Reset Password Sent</h2>
            </div>

            <div class="p-4 sm:p-7">

                <div class="mb-4 flex justify-center">
                    <span class="mb-4 inline-flex justify-center items-center  rounded-full ">
                        <x-icon.padlock class="w-[100px] h-[100px]">
                        </x-icon.padlock>
                    </span>
                </div>


                <div class="mt-5">

                    <x-secondary-button class="w-full flex justify-center gap-2 hover:bg-gray">
                        <x-icon.agent-white class="h-5 w-5 text-white"></x-icon.agent-white>
                        Resend Link
                    </x-secondary-button>



                </div>

                <div class="text-center">
                    <p class="mt-2 text-sm text-gray">
                        You will receive a password reset link if
                        <span class="text-white decoration-2 hover:underline font-medium dark:focus:outline-none dark:focus:ring-1 dark:focus:ring-gray-600" href="#">
                            hello@email.com
                        </span>
                        have an account with us.


                    </p>
                </div>
            </div>





        </div>
    </div>
</div>
