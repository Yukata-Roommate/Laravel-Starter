<x-yukata-rm::form method="post" :action="route('auth.logout.handle')">
    <x-yukata-rm::button color="secondary" :outline="true" type="submit" class="w-100">
        <i class="bi bi-power"></i>

        {{ __('yr-auth::button.logout') }}
    </x-yukata-rm::button>
</x-yukata-rm::form>
