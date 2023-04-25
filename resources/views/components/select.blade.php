@props([
    'disabled' => false,
    'placeholder' => 'Select',
    'options' => []
])

<select {{ $disabled ? 'disabled' : '' }} {!! $attributes->merge(['class' => 'border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm']) !!}>
    <option value="">{{ $placeholder }}</option>
    @foreach ($options as $id => $label)
        <option value="{{ $id }}">{{ $label }}</option>
    @endforeach
</select>
