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

namespace Sylius\Behat\Context\Setup;

use Behat\Behat\Context\Context;

final class CalendarContext implements Context
{
    public function __construct(private string $dateFilePath)
    {
    }

    /**
     * @Given it is :dateTime now
     */
    public function itIsNow(string $dateTime): void
    {
        file_put_contents($this->dateFilePath, $dateTime);
    }
}
