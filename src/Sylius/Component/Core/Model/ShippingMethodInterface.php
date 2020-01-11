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

interface ShippingMethodInterface extends BaseShippingMethodInterface, TaxableInterface, ChannelsAwareInterface
{
    public function getZone(): ?ZoneInterface;

    public function setZone(?ZoneInterface $zone): void;

    public function setTaxCategory(?TaxCategoryInterface $category): void;
}
