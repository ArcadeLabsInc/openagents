<x-app-layout>
    <x-slot name="header">
        <h2 class="pt-4 font-semibold text-xl leading-tight">
            {{ __('Profile') }}
        </h2>
    </x-slot>

    <div class="pb-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 flex justify-center">
            <div class="space-y-6">
                <div class="p-4 sm:p-8 bg-black shadow sm:rounded-lg">
                    <div class="max-w-xl mx-auto">
                        @include('profile.partials.update-profile-information-form')
                    </div>
                </div>

                <div class="p-4 sm:p-8 bg-black shadow sm:rounded-lg">
                    <div class="max-w-xl mx-auto">
                        @include('profile.partials.update-password-form')
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
