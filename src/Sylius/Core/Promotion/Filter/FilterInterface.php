<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Core\Promotion\Filter;

use Doctrine\Common\Collections\Collection;
use Sylius\Core\Model\OrderItemInterface;

/**
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 */
interface FilterInterface
{
    /**
     * @param array $items
     * @param array $configuration
     *
     * @return OrderItemInterface[]
     */
    public function filter(array $items, array $configuration);
}
