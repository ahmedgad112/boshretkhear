<?php

namespace App\Support;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class CodeGenerator
{
    public static function next(string $prefix, string $modelClass, string $column = 'code'): string
    {
        /** @var Model $modelClass */
        $latest = $modelClass::query()
            ->withTrashed()
            ->where($column, 'like', $prefix.'%')
            ->orderByDesc('id')
            ->value($column);

        $number = 1;

        if ($latest && preg_match('/(\d+)$/', $latest, $matches)) {
            $number = ((int) $matches[1]) + 1;
        }

        return $prefix.str_pad((string) $number, 5, '0', STR_PAD_LEFT);
    }

    public static function uniqueFileName(string $originalName): string
    {
        $extension = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));
        $safeExtension = in_array($extension, ['jpg', 'jpeg', 'png', 'webp', 'gif'], true) ? $extension : 'jpg';

        return Str::uuid()->toString().'.'.$safeExtension;
    }
}
