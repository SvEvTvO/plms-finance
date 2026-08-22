@props(['class' => ''])

<div class="animate-pulse rounded-md bg-[var(--color-muted-surface)] {{ $class }}"
     role="status" aria-label="Loading">
    <span class="sr-only">Loading...</span>
</div>

{{-- Contoh penggunaan: --}}
{{-- <x-skeleton class="h-4 w-32" /> --}}
{{-- <x-skeleton class="h-64 w-full" /> --}}
