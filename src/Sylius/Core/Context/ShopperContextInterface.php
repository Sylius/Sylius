<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Core\Context;

use Sylius\Core\Model\ChannelInterface;
use Sylius\Core\Model\CustomerInterface;

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
