<x-filament-panels::page>
    <div class="max-w-md mx-auto mt-10 space-y-6">
        <div class="text-center">
            <h2 class="text-2xl font-bold text-gray-800 dark:text-gray-200">Keamanan Lapis Dua (2FA)</h2>
            <p class="mt-2 text-sm text-gray-500">Silakan buka aplikasi Google Authenticator di handphone Anda dan
                masukkan 6 digit angka untuk memverifikasi identitas Anda.</p>
        </div>

        <form wire:submit="verify"
            class="p-8 space-y-6 bg-white shadow-xl rounded-xl dark:bg-gray-900 border border-gray-100 dark:border-gray-800">
            {{ $this->form }}
            <x-filament::button type="submit" class="w-full mt-4" size="lg">
                Verifikasi & Masuk Panel
            </x-filament::button>
        </form>
    </div>
</x-filament-panels::page>
