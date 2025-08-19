<x-yukata-rm::button.link color="info" :outline="true" :href="route('auth.reset-email.form')" :small="true" :block="true">
    <i class="bi bi-envelope"></i>

    <span class="d-sm-inline-block d-none">
        {{ __('yr-auth::button.reset-email') }}
    </span>
</x-yukata-rm::button.link>
