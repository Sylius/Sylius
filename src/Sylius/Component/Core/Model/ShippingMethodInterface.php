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

use Sylius\Component\Addressing\Model\ZoneInterface;
use Sylius\Component\Channel\Model\ChannelsAwareInterface;
use Sylius\Component\Shipping\Model\ShippingMethodInterface as BaseShippingMethodInterface;
use Sylius\Component\Taxation\Model\TaxableInterface;
use Sylius\Component\Taxation\Model\TaxCategoryInterface;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
interface ShippingMethodInterface extends BaseShippingMethodInterface, TaxableInterface, ChannelsAwareInterface
{
    /**
     * @return ZoneInterface
     */
    public function getZone();

    /**
     * @param ZoneInterface|null $zone
     */
    public function setZone(ZoneInterface $zone = null);

    /**
     * @param TaxCategoryInterface|null $category
     */
    public function setTaxCategory(TaxCategoryInterface $category = null);
}
