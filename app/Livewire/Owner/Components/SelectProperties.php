<?php

namespace App\Livewire\Owner\Components;

use App\Models\Owner;
use App\Models\Property;
use App\Livewire\Concerns\HasToast;
use App\Support\Toast;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Component;

class SelectProperties extends Component
{
    use HasToast;
    public int $selectedProperty;
    public Collection $properties;
    public Owner $owner;

    #[Computed]
    public function property(): Property
    {
        return $this->properties->firstWhere('id', $this->selectedProperty);
    }

    public function mount(): void
    {
        $this->owner = Owner::where('user_id', Auth::id())->firstOrFail();

        // Load all properties owned by the current user
        $this->properties = Property::where('owner_id', $this->owner->id)->get();

        // Set current selected property as the active_property
        $this->selectedProperty = $this->owner->active_property;
    }

    public function updatedSelectedProperty($value)
    {
        if ($this->owner->active_property != $value) {
            // Update active property
            $this->owner->update(['active_property' => $value]);
            Toast::success('Successfully Switched to your property!');
            // Refresh the page
            return redirect(route('owner.dashboard'));
        }
    }
}
