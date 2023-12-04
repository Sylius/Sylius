<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Sylius Sp. z o.o.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Behat\Page\Shop\Account\Order;

use FriendsOfBehat\PageObjectExtension\Page\SymfonyPageInterface;
use Sylius\Component\Core\Model\OrderInterface;

interface IndexPageInterface extends SymfonyPageInterface
{
    public function countOrders(): int;

    public function changePaymentMethod(OrderInterface $order);

    public function hasFlashMessage(string $message): bool;

    public function isOrderWithNumberInTheList(string $number): bool;

    public function openLastOrderPage(): void;
}
