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

namespace Sylius\Bundle\CoreBundle\Calculator;

use DateTimeInterface;
use Sylius\Bundle\CoreBundle\Exception\TargetTimeSmallerException;
use Symfony\Component\Messenger\Stamp\DelayStamp;

class DelayStampCalculator implements DelayStampCalculatorInterface
{
    /**
     * @throws TargetTimeSmallerException
     */
    public function calculate(?DateTimeInterface $currentTime, ?DateTimeInterface $targetTime): DelayStamp
    {
        if ($targetTime === null) {
            return new DelayStamp(0);
        }

        $timeDifference = $targetTime->getTimestamp() - $currentTime->getTimestamp();

        if ($timeDifference < 0) {
            $timeDifference = 0;
        }

        return new DelayStamp($timeDifference * 1000);
    }
}
