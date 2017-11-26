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

namespace Sylius\Behat\Page\Shop\Account\Order;

use Sylius\Behat\Page\SymfonyPageInterface;
use Sylius\Component\Core\Model\OrderInterface;

interface IndexPageInterface extends SymfonyPageInterface
{
    /**
     * @return int
     */
    public function countOrders(): int;

    /**
     * @param string $number
     *
     * @return bool
     */
    public function isOrderWithNumberInTheList(string $number): bool;

    /**
     *
     * @return bool
     */
    public function isItPossibleToChangePaymentMethodForOrder(OrderInterface $order): bool;

    public function openLastOrderPage(): void;
}
