<?php

namespace App\Filament\Moderator\Resources\UserResource\Pages;

use App\Filament\Moderator\Resources\UserResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model; // Tambahkan ini
use Carbon\Carbon; // Tambahkan ini

class EditUser extends EditRecord
{
    protected static string $resource = UserResource::class;

    // Siapkan penampung untuk data yang tidak masuk array save
    public ?string $newMutedUntil = null;
    public ?string $newSuspendedUntil = null;

    protected function getHeaderActions(): array
    {
        return [];
    }

    // Tangkap data form sebelum dibuang oleh Filament
    protected function mutateFormDataBeforeSave(array $data): array
    {
        $this->newMutedUntil = $data['muted_until'] ?? null;
        $this->newSuspendedUntil = $data['suspended_until'] ?? null;

        return $data;
    }

    // 🌟 FIX CLAUDE: Eksekusi perubahan hukuman menggunakan fungsi Model
    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        // Handle Mute
        if ($this->newMutedUntil) {
            $record->mute(Carbon::parse($this->newMutedUntil));
        } else {
            // Asumsi model punya fungsi unmute(). Jika tidak ada, gunakan: $record->update(['muted_until' => null]);
            if (method_exists($record, 'unmute')) {
                $record->unmute();
            } else {
                $record->forceFill(['muted_until' => null])->save();
            }
        }

        // Handle Suspend
        if ($this->newSuspendedUntil) {
            $record->suspend(Carbon::parse($this->newSuspendedUntil));
        } else {
            // Asumsi model punya fungsi unsuspend(). Jika tidak ada, forceFill
            if (method_exists($record, 'unsuspend')) {
                $record->unsuspend();
            } else {
                $record->forceFill(['suspended_until' => null])->save();
            }
        }

        return $record;
    }
}
