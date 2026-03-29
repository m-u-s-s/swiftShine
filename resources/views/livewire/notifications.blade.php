<div class="bg-white rounded-xl shadow border p-4">
    <div class="flex items-center justify-between mb-4">
        <div>
            <h3 class="text-lg font-semibold text-gray-800">Notifications</h3>
            <p class="text-sm text-gray-500">
                {{ $unreadCount }} non lue(s)
            </p>
        </div>

        @if($unreadCount > 0)
            <button
                wire:click="markAllAsRead"
                class="text-sm px-3 py-1 rounded bg-blue-600 text-white hover:bg-blue-700">
                Tout marquer comme lu
            </button>
        @endif
    </div>

    <div class="space-y-3">
        @forelse($notifications as $notification)
            @php
                $data = $notification->data ?? [];
                $message = $data['message'] ?? 'Notification';
                $date = $notification->created_at?->diffForHumans();
                $isUnread = is_null($notification->read_at);
            @endphp

            <div class="border rounded-lg p-3 {{ $isUnread ? 'bg-blue-50 border-blue-200' : 'bg-gray-50 border-gray-200' }}">
                <div class="flex items-start justify-between gap-3">
                    <div class="flex-1">
                        <p class="text-sm font-medium text-gray-800">
                            {{ $message }}
                        </p>

                        @if(!empty($data['rdv_id']))
                            <p class="text-xs text-gray-500 mt-1">
                                RDV #{{ $data['rdv_id'] }}
                            </p>
                        @endif

                        <p class="text-xs text-gray-400 mt-2">
                            {{ $date }}
                        </p>
                    </div>

                    <div class="flex items-center gap-2">
                        @if($isUnread)
                            <button
                                wire:click="markAsRead('{{ $notification->id }}')"
                                class="text-xs px-2 py-1 rounded bg-green-600 text-white hover:bg-green-700">
                                Lu
                            </button>
                        @endif

                        <button
                            wire:click="deleteNotification('{{ $notification->id }}')"
                            class="text-xs px-2 py-1 rounded bg-red-600 text-white hover:bg-red-700">
                            Supprimer
                        </button>
                    </div>
                </div>
            </div>
        @empty
            <div class="text-sm text-gray-500">
                Aucune notification pour le moment.
            </div>
        @endforelse
    </div>
</div>