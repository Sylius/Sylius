<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Sylius Sp. z o.o.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Tests\Api\Utils;

enum FilterTypes: string
{
    case Before = 'before';

    case StrictlyBefore = 'strictly_before';

    case After = 'after';

    case StrictlyAfter = 'strictly_after';
}
