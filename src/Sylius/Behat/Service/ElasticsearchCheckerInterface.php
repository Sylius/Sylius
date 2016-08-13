<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Behat\Service;

use Sylius\Behat\Exception\NotificationExpectationMismatchException;
use Sylius\Behat\NotificationType;

/**
 * @author Nicolas Adler <nicolas.adler@openizi.com>
 */
interface ElasticsearchCheckerInterface
{
    /**
     * {@inheritdoc}
     */
    public function waitForPendingRequests($timeout = 5);
}
