<?php

namespace App\Contracts;

use App\Models\TimeInterval;

interface IntervalProofProvider
{
    public static function getType(): int;

    public static function getName(): string;

    public function store(mixed $data, TimeInterval $interval): void;

    public function get(TimeInterval $interval): mixed;

    public function destroy(TimeInterval $interval): void;

    public function exists(TimeInterval $interval): bool;
}
