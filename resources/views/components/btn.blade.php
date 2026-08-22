@props(['variant' => 'primary', 'size' => 'md', 'type' => 'button'])

@php
$variants = [
    'primary' => 'bg-[var(--color-primary)] text-white hover:bg-teal-600',
    'secondary' => 'bg-[var(--color-secondary)] text-white hover:bg-cyan-600',
    'danger' => 'bg-[var(--color-danger)] text-white hover:bg-red-600',
    'ghost' => 'bg-transparent hover:bg-[var(--color-muted-surface)]',
];
$sizes = [
    'sm' => 'px-3 py-1.5 text-sm',
    'md' => 'px-4 py-2',
    'lg' => 'px-6 py-3 text-lg',
];
@endphp

<button type="{{ $type }}" {{ $attributes->merge([
    'class' => 'inline-flex items-center justify-center gap-2 font-medium rounded-lg transition ' . 
              ($variants[$variant] ?? '') . ' ' . ($sizes[$size] ?? '')
]) }}>
    {{ $slot }}
</button>
