<?php

namespace App\Contracts;

use App\Models\TimeInterval;

interface IntervalProofProvider extends VariationProviderContract
{
    public static function store(mixed $data, TimeInterval $interval): void;

    public static function get(TimeInterval $interval): mixed;

    public static function destroy(TimeInterval $interval): void;

    public static function exists(TimeInterval $interval): bool;
}
