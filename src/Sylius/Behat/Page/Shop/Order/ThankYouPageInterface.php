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

namespace Sylius\Behat\Page\Shop\Order;

use FriendsOfBehat\PageObjectExtension\Page\SymfonyPageInterface;

interface ThankYouPageInterface extends SymfonyPageInterface
{
    public function goToTheChangePaymentMethodPage(): void;

    public function goToOrderDetailsInAccount(): void;

    public function hasThankYouMessage(): bool;

    public function getInstructions(): string;

    public function hasInstructions(): bool;

    public function hasChangePaymentMethodButton(): bool;

    public function hasRegistrationButton(): bool;

    public function createAccount(): void;
}
