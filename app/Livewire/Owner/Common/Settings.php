<?php

namespace App\Livewire\Owner\Common;

use App\Livewire\Owner\Common\Settings\Account;
use App\Livewire\Owner\Common\Settings\PaymentConfiguration;
use App\Livewire\Owner\Common\Settings\PaymentRules;
use App\Support\Toast;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\WithPagination;
use Livewire\Component;

#[Layout('components.layouts.owner', ['title' => 'Settings'])]
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
            'payment' => PaymentConfiguration::class,
            'rules' => PaymentRules::class,
            default => Account::class,
        };

        if ($tab === Account::class) {
            $this->activeTab = 'account';
        }

        return $tab;
    }
    public function changeActiveProperty(int $propertyId): void
    {
        $owner = auth()->user()->owner;
        $owner->update(['active_property' => $propertyId]);
        Toast::success('You have successfully switched properties!');

        $this->redirectIntended(
            default: route('owner.dashboard', absolute: false),
            navigate: true
        );
    }
}
