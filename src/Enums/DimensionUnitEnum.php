<?php

declare(strict_types=1);

namespace MyParcelCom\ApiSdk\Enums;

use MyCLabs\Enum\Enum;

/**
 * @method static self MM3()
 * @method static self CM3()
 * @method static self DM3()
 */
class DimensionUnitEnum extends Enum
{
    const MM3 = 'mm3';
    const CM3 = 'cm3';
    const DM3 = 'dm3';
}
