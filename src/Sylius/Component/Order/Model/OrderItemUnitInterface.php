<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Order\Model;

/**
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 */
interface OrderItemUnitInterface extends AdjustableInterface
{
    /**
     * @return integer
     */
    public function getId();

    /**
     * @return int
     */
    public function getTotal();

    /**
     * @param int $total
     */
    public function setTotal($total);
}
