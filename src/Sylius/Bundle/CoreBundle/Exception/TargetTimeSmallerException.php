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

namespace Sylius\Bundle\CoreBundle\Exception;

use Throwable;

class TargetTimeSmallerException extends \Exception
{
    public function __construct(\DateTimeInterface $currentTime, \DateTimeInterface $targetTime)
    {
        parent::__construct(
            sprintf(
                'target time %s is smaller than current time %s',
                $targetTime->format('Y-m-d H:i:s'),
                $currentTime->format('Y-m-d H:i:s')
            )
        );
    }
}
