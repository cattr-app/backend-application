<?php

namespace App\Contracts;

interface VariationProviderContract
{
    public static function getMask(): int;

    public static function getName(): string;
}
