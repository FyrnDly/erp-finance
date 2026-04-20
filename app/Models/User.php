<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Filament\Panel;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable(['name', 'username', 'email', 'password'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable {
    use Notifiable, SoftDeletes;

    protected function casts(): array {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function groups(): BelongsToMany {
        return $this->belongsToMany(Group::class, 'group_user', 'user_id', 'group_id');
    }

    public function isManager(): bool {
        return $this->groups()->where('name', 'manager')->exists();
    }

    public function isStaff(): bool {
        return $this->groups()->where('name', 'staff')->exists();
    }

    public function canAccessPanel(Panel $panel): bool {
        return true;
    }
}
