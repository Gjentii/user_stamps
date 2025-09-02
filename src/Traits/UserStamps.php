<?php

namespace Gjentii\UserStamps\Traits;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

trait UserStamps
{
    public static function bootUserStamps(): void
    {
        static::creating(function (Model $model) {
            if (Auth::check()) {
                $model->created_by = Auth::id();
                $model->updated_by = Auth::id();
            }
        });

        static::updating(function (Model $model) {
            if (Auth::check()) {
                $model->updated_by = Auth::id();
            }
        });

        static::deleting(function (Model $model) {
            if (Auth::check() && method_exists($model, 'usesSoftDeletes') ? $model->usesSoftDeletes() : self::modelUsesSoftDeletes($model)) {
                $model->deleted_by = Auth::id();
                $model->saveQuietly();
            }
        });

        static::restoring(function (Model $model) {
            if (method_exists($model, 'usesSoftDeletes') ? $model->usesSoftDeletes() : self::modelUsesSoftDeletes($model)) {
                $model->deleted_by = null;
            }
        });
    }

    protected static function modelUsesSoftDeletes(Model $model): bool
    {
        return in_array('Illuminate\\Database\\Eloquent\\SoftDeletes', class_uses_recursive($model));
    }

    protected function userModelClass(): string
    {
        return (string) (config('auth.providers.users.model') ?? \App\Models\User::class);
    }

    public function createdBy()
    {
        return $this->belongsTo($this->userModelClass(), 'created_by');
    }

    public function updatedBy()
    {
        return $this->belongsTo($this->userModelClass(), 'updated_by');
    }

    public function deletedBy()
    {
        return $this->belongsTo($this->userModelClass(), 'deleted_by');
    }
}
