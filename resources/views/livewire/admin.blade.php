<div class="p-12">
    <div class="flex flex-row justify-between">
        <h2 class="mb-4">Users ({{ $totalUsers }})</h2>
        <form wire:submit="deleteMultiple" x-show="$wire.selectedUserIds.length > 0" x-cloak>
            <x-secondary-button type="submit">Delete</x-secondary-button>
        </form>
    </div>

    <div class="flex flex-col gap-8">
        <table class="min-w-full table-fixed divide-y divide-offblack text-white">
            <thead>
            <tr>
                <th>
                    {{-- Checkbox--}}
                </th>
                <th class="p-2 text-left text-sm font-semibold text-gray">
                    <div>ID</div>
                </th>
                <th class="p-2 text-left text-sm font-semibold text-gray">
                    <div>Name</div>
                </th>
                <th class="p-2 text-left text-sm font-semibold text-gray">
                    <div>Username</div>
                </th>
                <th class="p-2 text-left text-sm font-semibold text-gray">
                    <div>Email</div>
                </th>
                <th class="p-2 text-left text-sm font-semibold text-gray">
                    <div>LN Address</div>
                </th>
                <th class="p-2 text-left text-sm font-semibold text-gray">
                    <div># Messages</div>
                </th>
                <th class="p-2 text-left text-sm font-semibold text-gray">
                    <div>Pro</div>
                </th>
                <th class="p-2 text-left text-sm font-semibold text-gray">
                    <div>Joined</div>
                </th>
                <th class="p-2 text-left text-sm font-semibold text-gray">
                    {{-- Dropdown--}}
                </th>
            </tr>
            </thead>
            <tbody class="divide-y divide-offblack bg-black text-gray">
            @foreach($users as $user)
                <tr wire:key="{{ $user->id }}">
                    <td class="whitespace-nowrap p-2 text-sm">
                        <div class="flex items-center">
                            <input type="checkbox"
                                   wire:model="selectedUserIds"
                                   value="{{ $user->id }}"
                                   class="text-offblack focus:ring-0 active:bg-offblack focus:bg-offblack checked:bg-offblack rounded bg-black border-offblack shadow"/>
                        </div>
                    </td>
                    <td class=" whitespace-nowrap p-2 text-sm
                            ">{{ $user->id }}</td>
                    <td class="whitespace-nowrap p-2 text-sm">{{ $user->name }}</td>
                    <td class="whitespace-nowrap p-2 text-sm">{{ $user->username }}</td>
                    <td class="whitespace-nowrap p-2 text-sm">{{ $user->email }}</td>
                    <td class="whitespace-nowrap p-2 text-sm">{{ $user->lightning_address }}</td>
                    <td class="whitespace-nowrap p-2 text-sm">{{ $user->messages_count }}</td>
                    <td class="whitespace-nowrap p-2 text-sm">{{ $user->is_pro ? "Yes" : "No" }}</td>
                    <td class="whitespace-nowrap p-2 text-sm">{{ $user->dateForHumans() }}</td>
                    <td class="whitespace-nowrap p-2 text-sm">
                        <div class="flex items-center justify-end">
                            <x-admin.user-dropdown :user="$user"/>
                        </div>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
</div>
