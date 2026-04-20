<?php

namespace App\Filament\Pages;

use App\Enums\CoaType;
use App\Models\ChartOfAccount;
use App\Models\JournalEntry;
use App\Models\User;
use BackedEnum;
use Exception;
use Filament\Actions\Action;
use Filament\Forms;
use Filament\Forms\Components\Repeater;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Wizard;
use Filament\Schemas\Components\Wizard\Step;
use Filament\Schemas\Concerns\InteractsWithSchemas;
use Filament\Schemas\Contracts\HasSchemas;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Support\RawJs;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\HtmlString;

class OnBoardingPage extends Page implements HasSchemas {
    use InteractsWithSchemas;

    protected static ?string $slug = 'on-boarding';
    protected static ?string $navigationLabel = 'On Boarding';
    protected static string|BackedEnum|null $navigationIcon = Heroicon::Bolt;
    protected string $view = 'filament.pages.on-boarding';

    public User $user;
    public ?array $data;
    public bool $isManager;

    public static function canAccess(): bool {
        return ChartOfAccount::count() <= 1;
    }

    public function mount(): void {
        $this->user = Auth::user();
        $this->isManager = $this->user->isManager();
        $this->form->fill();
    }

    public function getHeading(): string|Htmlable|null {
        return "Satu Langkah Lagi Menuju Sistem ERP Finance yang Siap!";
    }

    public function getSubheading(): string|Htmlable|null {
        if ($this->isManager) {
            return "Halo, Manager. Kami mendeteksi bahwa Chart of Accounts (Buku Besar) Anda masih kosong. Sebagai fondasi utama laporan keuangan PT Anugerah Cahaya Chandra, Anda wajib menyusun daftar akun awal sebelum memulai pencatatan transaksi.";
        }

        return "Halo, Staff. Kami mendeteksi bahwa Chart of Accounts (Buku Besar) Anda masih kosong. Silahkan hubungi Manager Anda untuk mengisi Buku Besar sebagai fondasi utama laporan keuangan PT Anugerah Cahaya Chandra!";
    }

    public function getHeader(): ?View {
        return view('filament.custom.header');
    }

    public function form(Schema $schema): Schema {
        return $schema->components([
            Wizard::make([
                Step::make('coa')
                    ->label("Buku Besar (CoA)")
                    ->icon(Heroicon::BookOpen)
                    ->description("Gunakan nama yang spesifik untuk setiap Tipe, contoh: 'Kas Kecil' atau 'Beban Listrik'.")
                    ->schema($this->getCoaSchema()),
                Step::make('asset')
                    ->label("Saldo Awal")
                    ->icon(Heroicon::OutlinedBanknotes)
                    ->description("Masukkan total saldo terakhir dari pembukuan lama Anda. Jangan khawatir jika angkanya belum presisi, sistem akan otomatis menyeimbangkannya.")
                    ->schema($this->getAssetSchema())
            ])->submitAction(new HtmlString(Blade::render(<<<BLADE
                <x-filament::button type="submit" size="sm">
                    Simpan
                </x-filament::button>
            BLADE)))
        ])->statePath('data');
    }

    public function create() {
        $data = $this->form->getState();
        DB::transaction(function () use ($data) {
            try {
                $totalAsset = (int) $data['total_asset'];
                $totalLiability = (int) $data['total_liability'];
                $totalEquity = (int) $data['total_equity'];

                $asset = ChartOfAccount::create([
                    'name' => $data['asset'],
                    'type' => 'asset',
                    'description' => "Daftar Akun aset yang ditambahkan pertama kali oleh Manager"
                ]);

                $liability = ChartOfAccount::create([
                    'name' => $data['liability'],
                    'type' => 'liability',
                    'description' => "Daftar Akun kewajiban awal yang ditambahkan pertama kali oleh Manager"
                ]);
                
                $equity = ChartOfAccount::create([
                    'name' => $data['equity'],
                    'type' => 'equity',
                    'description' => "Daftar Akun ekuitas awal yang ditambahkan pertama kali oleh Manager"
                ]);

                foreach ($data['revenues'] as $name) {
                    ChartOfAccount::create([
                        'name' => $name,
                        'type' => 'revenue',
                        'description' => "Daftar Akun Pendapatan awal yang ditambahkan pertama kali oleh Manager"
                    ]);
                }
                
                foreach ($data['expenses'] as $name) {
                    ChartOfAccount::create([
                        'name' => $name,
                        'type' => 'expense',
                        'description' => "Daftar Akun Beban awal yang ditambahkan pertama kali oleh Manager"
                    ]);
                }

                $entry = JournalEntry::create([
                    'date' => now(),
                    'description' => "Saldo awal Akun Perusahaan dengan tambahan Saldo Penyeimbang, jika Total Aset lebih besar dari {$data['liability']} + {$data['equity']}",
                    'created_by' => $this->user->id,
                ]);

                $entry->items()->create([
                    'coa_id' => $asset->id,
                    'debit' => $totalAsset,
                ]);
                
                $entry->items()->create([
                    'coa_id' => $liability->id,
                    'credit' => $totalLiability,
                ]);
                
                $entry->items()->create([
                    'coa_id' => $equity->id,
                    'credit' => $totalEquity,
                ]);
                
                $totalCredit = $totalEquity + $totalLiability;
                $balance = $totalAsset >= $totalCredit 
                    ? $totalAsset - $totalCredit
                    : $totalCredit -  $totalAsset;

                if ($balance != 0) {
                    $equityCoaId = ChartOfAccount::where('name', 'Saldo Penyeimbang')->value('id');
                    $entry->items()->create([
                        'coa_id' => $equityCoaId,
                        'debit' => $totalAsset < $totalCredit ? $balance : 0,
                        'credit' => $totalAsset > $totalCredit ? $balance : 0,
                    ]);
                }

                return redirect(DashboardPage::getUrl());
            } catch (Exception $e) {
                Log::error('Gagal Inisialisasi Onboarding (Saldo Awal): ' . $e->getMessage(), [
                    'user_id' => $this->user->id,
                    'data_input' => $data,
                    'trace' => $e->getTraceAsString()
                ]);
                Notification::make()
                    ->danger()
                    ->title('Gagal Inisialisasi Sistem')
                    ->body("Silahkan hubungi Admin")
                    ->send();
            }
        });
    }

    public function getCoaSchema(): array {
        return [
            Forms\Components\TextInput::make("asset")
                ->label(CoaType::Asset->getLabel())
                ->helperText("Daftarkan aset perusahaan seperti Kas, Bank, atau Inventaris Stok. Akun ini mencerminkan kekayaan PT Anugerah Cahaya Chandra.")
                ->placeholder("contoh: Kas Utama")
                ->required(),
            Forms\Components\TextInput::make("liability")
                ->label(CoaType::Liability->getLabel())
                ->helperText("Masukkan daftar hutang usaha atau kewajiban jangka panjang. Penting untuk memantau jatuh tempo pembayaran.")
                ->placeholder("contoh: Hutang Perusahaan")
                ->required(),
            Forms\Components\TextInput::make("equity")
                ->label(CoaType::Equity->getLabel())
                ->helperText("Pencatatan modal pemilik atau saham perusahaan. Ini adalah sumber pendanaan internal Anda.")
                ->placeholder("contoh: Modal Awal")
                ->required(),
            Forms\Components\Repeater::make("revenues")
                ->label(CoaType::Revenue->getLabel())
                ->hint("Tentukan apa saja kategori pemasukan utama Anda")
                ->itemLabel(fn (array $state): ?string => $state['name'] ?? null)
                ->columnSpanFull()
                ->simple(
                    Forms\Components\TextInput::make("name")
                        ->placeholder("contoh: Penjualan Produk atau Jasa Konsultasi")
                        ->required(),
                )
                ->deleteAction(
                    fn (Action $action) => $action
                        ->action(function (array $arguments, Repeater $component): void {
                            $items = $component->getState();
                            $label = CoaType::Revenue->getLabel();
                            if (count($items) == 1) {
                                Notification::make()
                                    ->danger()
                                    ->title("Gagal Menghapus {$label}")
                                    ->body("Anda harus memiliki minimal 1 kategori {$label}")
                                    ->send();
                            } else {
                                unset($items[$arguments['item']]);
                                $component->state($items);
                            }
                        }),
                ),
            Forms\Components\Repeater::make("expenses")
                ->label(CoaType::Expense->getLabel())
                ->hint("Daftarkan pos pengeluaran rutin")
                ->itemLabel(fn (array $state): ?string => $state['name'] ?? null)
                ->columnSpanFull()
                ->simple(
                    Forms\Components\TextInput::make("name")
                        ->placeholder("contoh: Gaji Staf, Listrik, atau Biaya Operasional lainnya.")
                        ->required(),
                )
                ->deleteAction(
                    fn (Action $action) => $action
                        ->action(function (array $arguments, Repeater $component): void {
                            $items = $component->getState();
                            $label = CoaType::Expense->getLabel();
                            if (count($items) == 1) {
                                Notification::make()
                                    ->danger()
                                    ->title("Gagal Menghapus {$label}")
                                    ->body("Anda harus memiliki minimal 1 kategori {$label}")
                                    ->send();
                            } else {
                                unset($items[$arguments['item']]);
                                $component->state($items);
                            }
                        }),
                ),
        ];
    }
    
    public function getAssetSchema(): array {
        return [
            Forms\Components\TextInput::make("total_asset")
                ->label("Total ".CoaType::Asset->getLabel())
                ->helperText("Masukkan total dari seluruh aset (seperti Kas & Bank) yang Anda miliki saat ini.")
                ->mask(RawJs::make('$money($input)'))
                ->stripCharacters(",")
                ->prefix("Rp. ")
                ->suffix(".00")
                ->integer()
                ->required()
                ->minValue(0),
            Forms\Components\TextInput::make("total_liability")
                ->label("Total ".CoaType::Liability->getLabel())
                ->helperText("Total nilai seluruh hutang perusahaan yang belum dibayarkan hingga saat ini.")
                ->mask(RawJs::make('$money($input)'))
                ->stripCharacters(",")
                ->prefix("Rp. ")
                ->suffix(".00")
                ->integer()
                ->required()
                ->minValue(0),
            Forms\Components\TextInput::make("total_equity")
                ->label("Total ".CoaType::Equity->getLabel())
                ->helperText("Total nilai modal awal yang disetorkan atau laba yang ditahan oleh perusahaan.")
                ->mask(RawJs::make('$money($input)'))
                ->stripCharacters(",")
                ->prefix("Rp. ")
                ->suffix(".00")
                ->integer()
                ->required()
                ->minValue(0),
        ];
    }
}
