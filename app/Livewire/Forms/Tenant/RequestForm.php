<?php

namespace App\Livewire\Forms\Tenant;

use App\Models\Lease;
use App\Models\Request;
use Illuminate\Support\Facades\Auth;
use Livewire\Form;

class RequestForm extends Form
{
    public ?Lease $activeLease = null;
    public string $type = '';
    public string $description = '';
    public array $image_paths = [];

    protected function rules(): array
    {
        return [
            'type'        => ['required', 'in:maintenance,complaint,others'],
            'description' => ['required', 'string', 'min:10', 'max:1000'],
            'image_paths'      => ['nullable', 'array', 'max:3'],
            'image_paths.*'    => ['nullable', 'file', 'mimes:jpeg,png,jpg', 'max:5120'], // 5MB each
        ];
    }

    protected function messages(): array
    {
        return [
            'type.required' => 'The request type is required.',
            'type.in' => 'The selected request type is invalid.',
            'description.required' => 'The description is required.',
            'description.min' => 'The description must be at least :min characters.',
            'description.max' => 'The description may not be greater than :max characters.',
            'image_paths.array' => 'The images must be an array.',
            'image_paths.max' => 'You may not upload more than :max images.',
            'image_paths.*.mimes' => 'Each image must be a file of type: :values.',
            'image_paths.*.max' => 'Each image may not be greater than :max kilobytes.',
        ];
    }

    public function submit()
    {
        // Validate form data
        $this->validate();

        $tenant = Auth::user()->tenant;

        // Prepare folder
        $folderPath = "requests/tenant_{$tenant->id}";

        $storedImages = [];

        // Store uploaded images
        if (!empty($this->image_paths)) {
            foreach ($this->image_paths as $image) {
                $fileName = $image->getClientOriginalName();
                $path = $image->storeAs($folderPath, $fileName, 'public');

                $storedImages[] = $path;
            }
        }

        // Create database record
        Request::create([
            'tenant_id'   => $tenant->id,
            'unit_id'     => $this->activeLease->unit_id,
            'type'        => $this->type,
            'description' => $this->description,
            'image_path'  => $storedImages, // MUST be casted as array in model
        ]);
    }
}