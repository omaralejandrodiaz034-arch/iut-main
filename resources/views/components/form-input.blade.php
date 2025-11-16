@props(["name", "label" => null, "type" => 'text', "value" => null, "placeholder" => null, "help" => null, "required" => false, "list" => null, "options" => []])

@php
    $id = $attributes->get('id') ?? $name;
    $inputClasses = 'mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 shadow-sm focus:border-blue-500 focus:ring-blue-500';
    if($attributes->has('class')) {
        $inputClasses = $attributes->get('class') . ' ' . $inputClasses;
    }
@endphp

<div>
    @if($label)
        <label for="{{ $id }}" class="block text-sm font-medium text-gray-700">{{ $label }}@if($required) <span class="text-red-500">*</span>@endif</label>
    @endif

    @if($type === 'textarea')
        <textarea name="{{ $name }}" id="{{ $id }}" placeholder="{{ $placeholder }}" {{ $required ? 'required' : '' }} class="{{ $inputClasses }}">{{ $value }}</textarea>
    @else
        <input
            type="{{ $type }}"
            name="{{ $name }}"
            id="{{ $id }}"
            value="{{ $value }}"
            placeholder="{{ $placeholder }}"
            {{ $required ? 'required' : '' }}
            @if($list) list="{{ $list }}" data-datalist="{{ $list }}" @endif
            class="{{ $inputClasses }}"
            {{ $attributes->except(['class','id']) }}
        >
        @if($list && count($options) > 0)
            <datalist id="{{ $list }}">
                @foreach($options as $opt)
                    <option value="{{ $opt }}">{{ $opt }}</option>
                @endforeach
            </datalist>
        @endif
    @endif

    @if($help)
        <p class="text-xs text-gray-500 mt-1">{{ $help }}</p>
    @endif

    @error($name)
        <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
    @enderror

    <p class="text-sm text-red-600 mt-1 form-error" id="{{ $id }}-error" style="display:none;"></p>
</div>
