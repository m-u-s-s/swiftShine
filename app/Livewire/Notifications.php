<?php

namespace App\Livewire;

use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Notifications extends Component
{
    public $limit = 8;

    protected $listeners = [
        'notificationCreated' => '$refresh',
    ];

    public function markAsRead(string $notificationId): void
    {
        if (! Auth::check()) {
            return;
        }

        $notification = Auth::user()
            ->notifications()
            ->where('id', $notificationId)
            ->first();

        if ($notification && is_null($notification->read_at)) {
            $notification->markAsRead();
        }
    }

    public function markAllAsRead(): void
    {
        if (! Auth::check()) {
            return;
        }

        Auth::user()
            ->unreadNotifications
            ->markAsRead();
    }

    public function deleteNotification(string $notificationId): void
    {
        if (! Auth::check()) {
            return;
        }

        $notification = Auth::user()
            ->notifications()
            ->where('id', $notificationId)
            ->first();

        if ($notification) {
            $notification->delete();
        }
    }

    public function getUnreadCountProperty(): int
    {
        if (! Auth::check()) {
            return 0;
        }

        return Auth::user()
            ->unreadNotifications()
            ->count();
    }

    public function getNotificationsProperty()
    {
        if (! Auth::check()) {
            return collect();
        }

        return Auth::user()
            ->notifications()
            ->latest()
            ->take($this->limit)
            ->get();
    }

    public function render()
    {
        return view('livewire.notifications', [
            'notifications' => $this->notifications,
            'unreadCount' => $this->unreadCount,
        ]);
    }
}