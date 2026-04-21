<?php

namespace App\Filament\Resources\Invoices\Pages;

use App\Enums\CoaType;
use App\Enums\InvoiceStatus;
use App\Filament\Resources\Invoices\InvoiceResource;
use App\Models\ChartOfAccount;
use App\Models\Invoice;
use App\Models\JournalEntry;
use App\Models\User;
use Exception;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ViewInvoice extends ViewRecord
{
    protected static string $resource = InvoiceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
            
            Action::make('pay')
                ->label('Terima Pembayaran')
                ->color('success')
                ->icon('heroicon-m-banknotes')
                ->authorize('pay', clone $this->record)
                ->modalHeading('Penerimaan Pembayaran Invoice')
                ->modalDescription('Pilih akun penyimpanan (Aset) tempat uang pembayaran ini masuk. Sistem otomatis akan menjurnal pendapatan ini.')
                ->schema([
                    Select::make('receive_coa_id')
                        ->label('Simpan Uang Ke (Akun Aset)')
                        ->options(ChartOfAccount::where('type', CoaType::Asset->value)->pluck('name', 'id'))
                        ->searchable()
                        ->required(),
                ])
                ->action(function (array $data, Invoice $record) {
                    DB::transaction(function () use ($data, $record) {
                        /** @var User $user */
                        $user = Auth::user();
                        try {
                            $record->update([
                                'status' => InvoiceStatus::Paid,
                                'approved_by' => $user->id,
                            ]);

                            $totalInvoice = $record->items->sum('total');
                            $journal = JournalEntry::create([
                                'date' => now(),
                                'invoice_id' => $record->id,
                                'description' => "Penerimaan Pembayaran Invoice oleh {$user->name}: {$record->code} - {$record->subject}",
                                'created_by' => $user->id,
                            ]);

                            $journal->items()->create([
                                'coa_id' => $data['receive_coa_id'],
                                'debit' => $totalInvoice,
                            ]);

                            $journal->items()->create([
                                'coa_id' => $record->coa_id,
                                'credit' => $totalInvoice,
                            ]);

                            Notification::make()
                                ->success()
                                ->title('Pembayaran Diterima')
                                ->body('Status invoice lunas dan Jurnal Pendapatan berhasil dicatat.')
                                ->send();
                        } catch (Exception $e) {
                            Log::error('Gagal Menerima Pembayaran: ' . $e->getMessage(), [
                                'user_id' => $user->id,
                                'data_input' => $data,
                                'trace' => $e->getTraceAsString()
                            ]);

                            Notification::make()
                                ->danger()
                                ->title('Gagal Menerima Pembayaran')
                                ->body("Silahkan hubungi Admin")
                                ->send();
                        }
                    });
                }),
        ];
    }
}