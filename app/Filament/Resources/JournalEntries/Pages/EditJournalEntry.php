<?php

namespace App\Filament\Resources\JournalEntries\Pages;

use App\Filament\Resources\JournalEntries\JournalEntryResource;
use App\Filament\Resources\JournalEntries\Schemas\JournalEntryForm;
use Filament\Resources\Pages\EditRecord;
use Filament\Resources\Pages\EditRecord\Concerns\HasWizard;

class EditJournalEntry extends EditRecord
{
    use HasWizard;
    protected static string $resource = JournalEntryResource::class;

    public function getSteps(): array
    {
        return [
            JournalEntryForm::getJournal(),
            JournalEntryForm::getItem(),
        ];
    }
}
