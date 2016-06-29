<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Core\Taxation\Applicator;

use Sylius\Addressing\Model\ZoneInterface;
use Sylius\Core\Model\OrderInterface;

/**
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 * @author Mark McKelvie <mark.mckelvie@reiss.com>
 */
interface OrderTaxesApplicatorInterface
{
    /**
     * @param OrderInterface $order
     * @param ZoneInterface $zone
     */
    public function apply(OrderInterface $order, ZoneInterface $zone);
}
