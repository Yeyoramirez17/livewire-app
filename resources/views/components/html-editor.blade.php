<div x-data="{ value: @entangle($attributes->wire('model')).defer }"
     x-on:trix-change="value = $event.target.value">
    <div wire:ignore>
        <trix-editor :value="value" {!! $attributes->whereDoesntStartWith('wire:model')->merge([
            'class' => 'border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm',
        ]) !!}></trix-editor>
    </div>
</div>
