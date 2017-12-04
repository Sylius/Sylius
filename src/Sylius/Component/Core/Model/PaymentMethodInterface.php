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

namespace Sylius\Component\Core\Model;

use Payum\Core\Model\GatewayConfigInterface;
use Sylius\Component\Channel\Model\ChannelsAwareInterface;
use Sylius\Component\Payment\Model\PaymentMethodInterface as BasePaymentMethodInterface;

interface PaymentMethodInterface extends BasePaymentMethodInterface, ChannelsAwareInterface
{
    /**
     * @param GatewayConfigInterface|null $gateway
     */
    public function setGatewayConfig(?GatewayConfigInterface $gateway): void;

    /**
     * @return GatewayConfigInterface|null
     */
    public function getGatewayConfig(): ?GatewayConfigInterface;
}
