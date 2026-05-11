<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class Client extends Model
{
    use HasFactory;
    protected $fillable = ['name'];

    public function contracts(): HasMany
    {
        return $this->hasMany(Contract::class);
    }

    public function contacts(): HasMany
    {
        return $this->hasMany(Contact::class);
    }

    public function certificates(): HasMany
    {
        return $this->hasMany(Certificate::class);
    }

    public function mappings(): HasMany
    {
        return $this->hasMany(ClientMapping::class);
    }

    public function aliases(): HasMany
    {
        return $this->hasMany(ClientAlias::class);
    }

    public function auditResults(): HasMany
    {
        return $this->hasMany(AiAuditResult::class);
    }

    public function codCertificates(): HasMany
    {
        return $this->hasMany(CodCertificate::class);
    }

    public function inventoryDaemons(): HasMany
    {
        return $this->hasMany(LicenseInventoryDaemon::class);
    }
}

