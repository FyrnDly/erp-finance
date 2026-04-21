<?php

namespace App\Filament\Resources\Invoices\Pages;

use App\Filament\Resources\Invoices\InvoiceResource;
use App\Filament\Resources\Invoices\Schemas\InvoiceForm;
use Filament\Resources\Pages\CreateRecord;
use Filament\Resources\Pages\CreateRecord\Concerns\HasWizard;

class CreateInvoice extends CreateRecord
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
