<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Core\Promotion\Filter;

use Doctrine\Common\Collections\Collection;
use Sylius\Component\Core\Model\OrderItemInterface;

/**
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 */
interface TaxonFilterInterface
{
    /**
     * @param array $items
     * @param array $configuration
     *
     * @return OrderItemInterface[]
     */
    public function filter(array $items, array $configuration);
}
