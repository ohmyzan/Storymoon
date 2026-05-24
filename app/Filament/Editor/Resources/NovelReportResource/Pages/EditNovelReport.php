<?php

namespace App\Filament\Editor\Resources\NovelReportResource\Pages;

use App\Filament\Editor\Resources\NovelReportResource;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;

class EditNovelReport extends EditRecord
{
    protected static string $resource = NovelReportResource::class;

    // 🌟 FIX: Menangani logika 'escalated', 'resolved', 'rejected' 
    // dengan memanggil method bawaan Model agar tidak terkena blokir $fillable
    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        // Tangkap catatan editor
        $notes = $data['editor_notes'] ?? '';
        $user = auth()->user();

        // Eksekusi berdasarkan pilihan status
        if ($data['status'] === 'escalated') {
            $record->escalate($user, $notes);
        } elseif ($data['status'] === 'resolved') {
            $record->resolve($user, $notes);
        } elseif ($data['status'] === 'rejected') {
            $record->reject($user, $notes);
        } else {
            // Jika status = 'reviewed' atau 'pending'
            $record->update([
                'status' => $data['status'],
                'editor_notes' => $notes,
                'handled_by' => $user->id,
            ]);
        }

        return $record;
    }
}
