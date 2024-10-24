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

namespace Sylius\Bundle\CoreBundle\Calculator;

use DateTimeInterface;
use Symfony\Component\Messenger\Stamp\DelayStamp;

interface DelayStampCalculatorInterface
{
    public function calculate(?DateTimeInterface $currentTime, ?DateTimeInterface $targetTime): DelayStamp;
}
