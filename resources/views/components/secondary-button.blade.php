<button {{ $attributes->merge(['type' => 'button', 'class' => 'cp-button-secondary focus-visible:ring-2 focus-visible:ring-emerald-600 focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50']) }}>
    {{ $slot }}
</button>
