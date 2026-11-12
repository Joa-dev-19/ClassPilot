<button {{ $attributes->merge(['type' => 'submit', 'class' => 'cp-button-primary focus-visible:ring-2 focus-visible:ring-emerald-600 focus-visible:ring-offset-2']) }}>
    {{ $slot }}
</button>
