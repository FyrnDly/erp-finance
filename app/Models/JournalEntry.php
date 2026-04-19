<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable(['code', 'date', 'invoice_id', 'expense_id', 'description', 'created_by'])]
class JournalEntry extends Model {
    use SoftDeletes;

    public function invoice(): BelongsTo {
        return $this->belongsTo(Invoice::class, 'invoice_id', 'id');
    }
    
    public function expense(): BelongsTo {
        return $this->belongsTo(Expense::class, 'expense_id', 'id');
    }
    
    public function items(): HasMany {
        return $this->hasMany(JournalItem::class, 'entry_id', 'id');
    }
    
    public function user(): BelongsTo {
        return $this->belongsTo(User::class, 'created_by', 'id');
    }

    protected static function booted(): void {
        parent::booted();

        static::creating(function ($model) {
            $date = now()->format('my');
            $source = 'SELF';
            if ($model->invoice_id) $source = 'INV';
            if ($model->expense_id) $source = 'EXP';
            if (request()->routeIs('setup-wizard.*')) $source = 'OPEN';
            
            $prefix = "JE/{$source}/{$date}";
            $latest = self::where('code', 'ilike', "{$prefix}/%")
                ->whereMonth('date', now()->month)
                ->whereYear('date', now()->year)
                ->orderBy('code', 'desc')
                ->value('code');

            if ($latest) {
                $num = (int) substr($latest, -4);
                $num = sprintf('%04d', $num + 1);
            } else {
                $num = '0001';
            }

            $model->code = "{$prefix}/{$num}";
        });
    }
}
