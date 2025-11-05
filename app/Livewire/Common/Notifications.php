<?php

namespace App\Livewire\Common;

use App\Models\Notification;
use Livewire\Attributes\Computed;
use Livewire\Component;

class Notifications extends Component
{

    public string $selectedTab = 'all';

    #[Computed]
    public function notifications()
    {
        return Notification::where('user_id', auth()->id())
            ->when($this->selectedTab === 'unread', fn($query) => $query->where('is_read', false))
            ->orderByDesc('created_at')
            ->get();
    }

     /** Switch to show all notifications */
    public function showAll(): void
    {
        $this->selectedTab = 'all';
    }

    /** Switch to show only unread notifications */
    public function showUnread(): void
    {
        $this->selectedTab = 'unread';
    }

    /** Mark a single notification as read (triggered on click) */
    public function markAsRead(int $notificationId): void
    {
        $notif = Notification::where('user_id', auth()->id())->find($notificationId);
        if ($notif && !$notif->is_read) {
            $notif->update(['is_read' => true]);
        }
    }

    /** Mark all notifications as read */
    public function markAllAsRead(): void
    {
        Notification::where('user_id', auth()->id())
            ->where('is_read', false)
            ->update(['is_read' => true]);
    }

    /** Delete all notifications */
    public function deleteAllNotifications(): void
    {
        Notification::where('user_id', auth()->id())->delete();
    }
}
