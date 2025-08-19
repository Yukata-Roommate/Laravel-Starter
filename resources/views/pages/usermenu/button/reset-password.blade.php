<x-yukata-rm::button.link color="info" :outline="true" :href="route('auth.reset-password.form')" :small="true" :block="true">
    <i class="bi bi-lock-fill"></i>

    <span class="d-sm-inline-block d-none">
        {{ __('yr-auth::button.reset-password') }}
    </span>
</x-yukata-rm::button.link>
