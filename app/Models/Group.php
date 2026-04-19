<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable(['name'])]
class Group extends Model {
    use SoftDeletes;

    public function users(): BelongsToMany {
        return $this->belongsToMany(User::class, 'group_user', 'group_id', 'user_id');
    }
}
