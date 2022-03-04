<?php

namespace App\Enums;

use BenSampo\Enum\Enum;

/**
 * @method static static EMPLOYEE()
 * @method static static CLIENT()
 */
final class UserType extends Enum
{
    public const EMPLOYEE = 0;
    public const CLIENT = 1;
}
