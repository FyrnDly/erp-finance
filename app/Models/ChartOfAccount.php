<?php

namespace App\Models;

use App\Enums\CoaType;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable(['code', 'name', 'type', 'description'])]
class ChartOfAccount extends Model {
    use SoftDeletes;

    protected $casts = [
        'type' => CoaType::class
    ];

    public function expenses(): HasMany {
        return $this->hasMany(Expense::class, 'coa_id', 'id');
    }
    
    public function invoices(): HasMany {
        return $this->hasMany(Invoice::class, 'coa_id', 'id');
    }
    
    public function items(): HasMany {
        return $this->hasMany(JournalItem::class, 'coa_id', 'id');
    }

    protected static function booted(): void {
        parent::booted();

        static::creating(function ($model) {
            $type = strtoupper($model->type->value);
            $prefix = "COA/{$type}";

            $latest = self::where('code', 'ilike', "{$prefix}/%")
                ->where('type', $model->type->value)
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
