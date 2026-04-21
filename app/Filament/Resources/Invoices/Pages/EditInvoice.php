<?php

namespace App\Filament\Resources\Invoices\Pages;

use App\Filament\Resources\Invoices\InvoiceResource;
use App\Filament\Resources\Invoices\Schemas\InvoiceForm;
use Filament\Resources\Pages\EditRecord;
use Filament\Resources\Pages\EditRecord\Concerns\HasWizard;

class EditInvoice extends EditRecord
{
    use HasWizard;
    protected static string $resource = InvoiceResource::class;

    public function getSteps(): array
    {
        return [
            InvoiceForm::getInvoice(),
            InvoiceForm::getItem()
        ];
    }
}
