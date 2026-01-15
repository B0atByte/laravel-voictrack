<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class File extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'filename',
        'requester',
        'expiry_date',
        'uploaded_at',
    ];

    protected $casts = [
        'expiry_date' => 'date',
        'uploaded_at' => 'datetime',
    ];

    public function scopeActive($query)
    {
        return $query->where('expiry_date', '>=', Carbon::today());
    }

    public function scopeExpired($query)
    {
        return $query->where('expiry_date', '<', Carbon::today());
    }

    public function scopeByCode($query, $code)
    {
        return $query->where('code', $code);
    }

    public function isExpired()
    {
        return $this->expiry_date < Carbon::today();
    }

    public function getFilePathAttribute()
    {
        return storage_path('app/uploads/' . $this->filename);
    }
}
