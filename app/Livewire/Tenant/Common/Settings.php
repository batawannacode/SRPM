<?php

namespace App\Livewire\Tenant\Common;

use App\Livewire\Tenant\Common\Settings\Account;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\WithPagination;
use Livewire\Component;

#[Layout('components.layouts.tenant', ['title' => 'Settings'])]
class Settings extends Component
{
    use WithPagination;

    #[Url(as: 'tab', keep: true)]
    public string $activeTab = 'account';

    #[Computed]
    public function activeTabs(): string
    {
        $tab = match ($this->activeTab) {
            'account' => Account::class,
            default => Account::class,
        };

        if ($tab === Account::class) {
            $this->activeTab = 'account';
        }

        return $tab;
    }
}
