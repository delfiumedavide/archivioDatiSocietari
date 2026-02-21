<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable, SoftDeletes;

    protected $fillable = [
        'name',
        'email',
        'password',
        'is_active',
        'last_login_at',
        'last_login_ip',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'last_login_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
        ];
    }

    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class);
    }

    public function permissions(): BelongsToMany
    {
        return $this->belongsToMany(Permission::class);
    }

    public function companies(): BelongsToMany
    {
        return $this->belongsToMany(Company::class)->withTimestamps();
    }

    public function activityLogs(): HasMany
    {
        return $this->hasMany(ActivityLog::class);
    }

    public function documents(): HasMany
    {
        return $this->hasMany(Document::class, 'uploaded_by');
    }

    public function pushSubscriptions(): HasMany
    {
        return $this->hasMany(PushSubscription::class);
    }

    public function hasRole(string ...$roles): bool
    {
        return $this->roles->contains(fn (Role $role) => in_array($role->name, $roles));
    }

    public function isAdmin(): bool
    {
        return $this->hasRole('admin');
    }

    public function isManager(): bool
    {
        return $this->hasRole('manager');
    }

    public function hasPermission(string $permission): bool
    {
        if ($this->isAdmin()) {
            return true;
        }

        return $this->permissions->contains('name', $permission);
    }

    public function hasSection(string $section): bool
    {
        if ($this->isAdmin()) {
            return true;
        }

        return $this->permissions
            ->where('section', $section)
            ->isNotEmpty();
    }

    /**
     * Restituisce null per admin (nessuna restrizione),
     * oppure l'array di company_id assegnate all'utente.
     * Chiamare $user->load('companies') prima per evitare N+1.
     */
    public function accessibleCompanyIds(): ?array
    {
        if ($this->isAdmin()) {
            return null;
        }

        return $this->companies->pluck('id')->all();
    }

    public function canAccessCompany(Company|int $company): bool
    {
        if ($this->isAdmin()) {
            return true;
        }

        $id = $company instanceof Company ? $company->id : $company;
        $ids = $this->accessibleCompanyIds();

        return $ids !== null && in_array($id, $ids);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
