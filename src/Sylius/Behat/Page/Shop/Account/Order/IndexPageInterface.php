<?php

/*
 * This file is a part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Behat\Page\Shop\Account\Order;

use Sylius\Behat\Page\SymfonyPageInterface;

/**
 * @author Grzegorz Sadowski <grzegorz.sadowski@lakion.com>
 */
interface IndexPageInterface extends SymfonyPageInterface
{
    /**
     * @return int
     */
    public function countOrders();

    /**
     * @param string $number
     *
     * @return bool
     */
    public function isOrderWithNumberInTheList($number);
}
