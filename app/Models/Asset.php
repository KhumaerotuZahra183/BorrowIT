<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Asset extends Model
{
    protected $fillable = [
        'asset_id',
        'asset_number',
        'asset_name',
        'available',
    ];

    protected static function booted(): void
    {
        static::creating(function (Asset $asset) {
            if (!$asset->asset_id) {
                $asset->asset_id = self::generateAssetId();
            }
        });
    }

    public static function generateAssetId(): string
    {
        $year = now()->format('y');
        $prefix = "BPI-{$year}-";
        $last = self::query()
            ->where('asset_id', 'like', $prefix . '%')
            ->orderByDesc('asset_id')
            ->value('asset_id');

        $nextNumber = 1;
        if ($last) {
            $parts = explode('-', $last);
            $lastNumber = (int) end($parts);
            $nextNumber = $lastNumber + 1;
        }

        return $prefix . str_pad((string) $nextNumber, 4, '0', STR_PAD_LEFT);
    }
}
