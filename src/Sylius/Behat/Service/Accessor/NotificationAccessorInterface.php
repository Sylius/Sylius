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

namespace Sylius\Behat\Service\Accessor;

use Sylius\Behat\NotificationType;

interface NotificationAccessorInterface
{
    /**
     * @return string
     */
    public function getMessage(): string;

    /**
     * @return NotificationType
     */
    public function getType(): NotificationType;
}
