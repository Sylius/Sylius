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

namespace Sylius\Behat\Page\Shop\Order;

use Sylius\Behat\Page\SymfonyPageInterface;

interface ShowPageInterface extends SymfonyPageInterface
{
    public function hasPayAction(): bool;

    public function pay();

    public function choosePaymentMethod(string $paymentMethodName);

    /**
     * @return string[]
     */
    public function getNotifications(): array;

    public function getNumberOfItems(): int;
}
