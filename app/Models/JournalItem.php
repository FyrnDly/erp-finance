<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['entry_id', 'coa_id', 'debit', 'credit'])]
class JournalItem extends Model {
    public function entry(): BelongsTo {
        return $this->belongsTo(JournalEntry::class, 'entry_id', 'id');
    }
    
    public function coa(): BelongsTo {
        return $this->belongsTo(ChartOfAccount::class, 'coa_id', 'id');
    }
}
