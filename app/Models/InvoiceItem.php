<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['invoice_id', 'name', 'quantity', 'price', 'total'])]
class InvoiceItem extends Model {
    public function invoice(): BelongsTo {
        return $this->belongsTo(Invoice::class, 'invoice_id', 'id');
    }
}
