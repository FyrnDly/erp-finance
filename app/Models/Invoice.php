<?php

namespace App\Models;

use App\Enums\InvoiceStatus;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable(['code', 'subject', 'issue_date', 'due_date', 'status', 'coa_id', 'submitted_by', 'approved_by'])]
class Invoice extends Model {
    use SoftDeletes;

    protected $casts = [
        'status' => InvoiceStatus::class
    ];

    public function coa(): BelongsTo {
        return $this->belongsTo(ChartOfAccount::class, 'coa_id', 'id');
    }

    public function journals(): HasMany {
        return $this->hasMany(JournalEntry::class, 'invoice_id', 'id');
    }
    
    public function submit(): BelongsTo {
        return $this->belongsTo(User::class, 'submitted_by', 'id');
    }
    
    public function approve(): BelongsTo {
        return $this->belongsTo(User::class, 'approved_by', 'id');
    }

    public function items(): HasMany {
        return $this->hasMany(InvoiceItem::class, 'invoice_id', 'id');
    }

    protected static function booted(): void {
        parent::booted();

        static::creating(function ($model) {
            $date = now()->format('my');
            $prefix = "INV/{$date}";

            $latest = self::where('code', 'ilike', "{$prefix}/%")
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
