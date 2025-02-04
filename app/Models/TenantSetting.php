<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TenantSetting extends Model
{
    protected $guarded = [];
    protected $casts = [
        'data' => 'array'
    ];

// relation to settings.
    public function setting()
    {
        return $this->belongsTo(Setting::class);
    }

// relation to tenant.
    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }
}
