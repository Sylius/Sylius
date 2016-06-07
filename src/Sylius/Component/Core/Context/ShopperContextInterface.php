<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Core\Context;

use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\CustomerInterface;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
interface ShopperContextInterface
{
    /**
     * @return ChannelInterface
     */
    public function getChannel();

    /**
     * @return string
     */
    public function getCurrencyCode();

    /**
     * @return string
     */
    public function getLocaleCode();

    /**
     * @return CustomerInterface|null
     */
    public function getCustomer();
}
