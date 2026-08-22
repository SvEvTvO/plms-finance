@props(['padding' => 'p-6'])

<div {{ $attributes->merge([
    'class' => 'rounded-xl border border-[var(--color-border)] bg-[var(--color-surface)] shadow-sm ' . $padding
]) }}>
    {{ $slot }}
</div>
