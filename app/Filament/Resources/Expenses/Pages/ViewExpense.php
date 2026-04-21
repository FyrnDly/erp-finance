<?php

namespace App\Filament\Resources\Expenses\Pages;

use App\Enums\CoaType;
use App\Filament\Resources\Expenses\ExpenseResource;
use App\Models\ChartOfAccount;
use App\Models\Expense;
use App\Models\JournalEntry;
use App\Models\User;
use Exception;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Support\Enums\Width;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ViewExpense extends ViewRecord
{
    protected static string $resource = ExpenseResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
            Action::make('verify')
                ->label('Verifikasi')
                ->color('info')
                ->icon(Heroicon::OutlinedPencilSquare)
                ->authorize('verify', clone $this->record)
                ->requiresConfirmation()
                ->modalWidth(Width::TwoExtraLarge)
                ->modalIcon(Heroicon::OutlinedPencilSquare)
                ->modalHeading('Konfirmasi Pengajuan Pengeluaran')
                ->modalDescription('Jika Anda menyetujui pengeluaran ini, Sistem akan otomatis membuat Jurnal Umum.')
                ->schema([
                    Select::make('status')
                        ->label('Status Verifikasi')
                        ->native(false)
                        ->required()
                        ->live()
                        ->options([
                            'approved' => 'Disetujui',
                            'rejected' => 'Ditolak'
                        ]),
                    Select::make('coa_id')
                        ->label('Sumber Dana Pembayaran')
                        ->helperText('Hanya menampilkan akun bertipe Aset')
                        ->native(false)
                        ->searchable()
                        ->required()
                        ->visible(fn(Get $get) => $get('status') == 'approved')
                        ->options(ChartOfAccount::where('type', CoaType::Asset->value)->pluck('name', 'id')),
                    Textarea::make('refusal')
                        ->label('Alasan Penolakan')
                        ->visible(fn(Get $get) => $get('status') == 'rejected')
                        ->required()
                        ->rows(3)
                ])
                ->action(function (array $data, Expense $record) {
                    DB::transaction(function () use ($data, $record) {
                        /** @var User $user */
                        $user = Auth::user();
                        try {
                            $data['approved_date'] = now();
                            $data['approved_by'] = $user->id;
                            $coa_id = $data['status'] === 'approved'
                                ? ($data['coa_id'] ?? null)
                                : null;
                                
                            unset($data['coa_id']);
                            $record->update($data);
                            if ($data['status'] == 'approved') {
                                $entry = JournalEntry::create([
                                    'date' => now(),
                                    'expense_id' => $record->id,
                                    'description' => "Pencairan Expense oleh {$user->name}: {$record->code} - {$record->description}",
                                    'created_by' => $user->id
                                ]);

                                $entry->items()->create([
                                    'coa_id' => $record->coa_id,
                                    'debit' => $record->amount,
                                    'credit' => 0
                                ]);
                                
                                $entry->items()->create([
                                    'coa_id' => $coa_id,
                                    'debit' => 0,
                                    'credit' => $record->amount
                                ]);
                            }

                            Notification::make('success')
                                ->title('Berhasil Disetujui')
                                ->body('Pengeluaran telah disetujui dan Jurnal pencairan dana berhasil dibuat otomatis.')
                                ->success()
                                ->send();

                        } catch (Exception $e) {
                            Log::error('Gagal Verifikasi Pengeluaran: ' . $e->getMessage(), [
                                'user_id' => $user->id,
                                'data_input' => $data,
                                'trace' => $e->getTraceAsString()
                            ]);

                            Notification::make()
                                ->danger()
                                ->title('Gagal Verifikasi Pengeluaran')
                                ->body("Silahkan hubungi Admin")
                                ->send();
                        }
                    });
                })
        ];
    }
}
