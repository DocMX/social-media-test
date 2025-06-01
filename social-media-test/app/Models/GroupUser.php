<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GroupUser extends Model
{
    use HasFactory;

    const UPDATED_AT = null;

    protected $fillable = [
        'status',
        'role',
        'user_id',
        'group_id',
        'created_by',
        'token',
        'token_expire_date',
    ];

    public function adminUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function group(): BelongsTo
    {
        return $this->belongsTo(Group::class);
    }

    public function isValidToken($plainToken)
    {
        return hash_equals($this->token, hash('sha256', $plainToken)) &&
            Carbon::now()->lt($this->token_expire_date);
    }
}
