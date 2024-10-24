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

namespace Sylius\Bundle\CoreBundle\OrderPay\Handler;

use Sylius\Bundle\ResourceBundle\Controller\RequestConfiguration;

/** @experimental */
interface PaymentStateFlashHandlerInterface
{
    public function handle(RequestConfiguration $requestConfiguration, string $state): void;
}
