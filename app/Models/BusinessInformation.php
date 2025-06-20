<?php

namespace App\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Cache;

class BusinessInformation extends Model {
    use HasFactory, Notifiable, SoftDeletes;

    protected $guarded = [];

    protected function casts(): array {
        return [
            'id'                 => 'integer',
            'user_id'            => 'integer',
            'avatar'             => 'string',
            'name'               => 'string',
            'bio'                => 'string',
            'business_name'      => 'string',
            'business_address'   => 'string',
            'professional_title' => 'string',
            'license'            => 'string',
            'status'             => 'string',
            'created_at'         => 'datetime',
            'updated_at'         => 'datetime',
            'deleted_at'         => 'datetime',
        ];
    }

    protected static function booted(): void {
        static::saved(function () {
            Cache::forget('top_beauty_experts');
        });

        static::deleted(function () {
            Cache::forget('top_beauty_experts');
        });
    }

    /**
     * Scope for active business information with non-banned users
     */
    public function scopeActive(Builder $q): Builder {
        return $q->where('status', 'active')->whereHas('user');
    }

    public function user(): BelongsTo {
        return $this->belongsTo(User::class);
    }
}
