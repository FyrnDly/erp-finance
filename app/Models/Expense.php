<?php

namespace App\Models;

use App\Enums\ExpenseStatus;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable(['code', 'status', 'amount', 'date', 'approved_date', 'description', 'coa_id', 'submitted_by', 'approved_by'])]
class Expense extends Model {
    use SoftDeletes;

    protected $casts = [
        'status' => ExpenseStatus::class
    ];

    public function coa(): BelongsTo {
        return $this->belongsTo(ChartOfAccount::class, 'coa_id', 'id');
    }

    public function journals(): HasMany {
        return $this->hasMany(JournalEntry::class, 'expense_id', 'id');
    }
    
    public function submit(): BelongsTo {
        return $this->belongsTo(User::class, 'submitted_by', 'id');
    }
    
    public function approve(): BelongsTo {
        return $this->belongsTo(User::class, 'approved_by', 'id');
    }

    protected static function booted(): void {
        parent::booted();

        static::creating(function ($model) {
            $date = now()->format('my');
            $prefix = "EXP/{$date}";

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
