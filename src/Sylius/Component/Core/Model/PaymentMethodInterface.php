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

/**
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 */
interface PaymentMethodInterface extends BasePaymentMethodInterface, ChannelsAwareInterface
{
    /**
     * @param GatewayConfigInterface $gateway
     */
    public function setGatewayConfig(GatewayConfigInterface $gateway);

    /**
     * @return GatewayConfigInterface
     */
    public function getGatewayConfig();
}
