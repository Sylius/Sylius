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

namespace Sylius\Component\Core\Context;

use Sylius\Component\Channel\Context\ChannelContextInterface;
use Sylius\Component\Currency\Context\CurrencyContextInterface;
use Sylius\Component\Customer\Context\CustomerContextInterface;
use Sylius\Component\Locale\Context\LocaleContextInterface;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
interface ShopperContextInterface extends
    ChannelContextInterface,
    CurrencyContextInterface,
    LocaleContextInterface,
    CustomerContextInterface
{
}
