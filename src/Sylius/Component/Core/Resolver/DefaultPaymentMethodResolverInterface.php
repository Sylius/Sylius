<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Core\Resolver;

use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\PaymentMethodInterface;

/**
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 */
interface DefaultPaymentMethodResolverInterface
{
    /**
     * @param ChannelInterface $channel
     *
     * @return PaymentMethodInterface
     */
    public function getDefaultPaymentMethodByChannel(ChannelInterface $channel);
}
