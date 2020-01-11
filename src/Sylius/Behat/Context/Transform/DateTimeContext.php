<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Behat\Context\Transform;

use Behat\Behat\Context\Context;

final class DateTimeContext implements Context
{
    /**
     * @Transform :date
     * @Transform :startsDate
     * @Transform :endsDate
     */
    public function getDate($date)
    {
        return new \DateTime($date);
    }
}
