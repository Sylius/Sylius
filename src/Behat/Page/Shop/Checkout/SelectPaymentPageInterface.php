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

namespace Sylius\Behat\Page\Shop\Checkout;

use FriendsOfBehat\PageObjectExtension\Page\SymfonyPageInterface;

interface SelectPaymentPageInterface extends SymfonyPageInterface
{
    public function selectPaymentMethod(string $paymentMethod): void;

    public function hasPaymentMethod(string $paymentMethodName): bool;

    public function getItemSubtotal(string $itemName): string;

    public function nextStep(): void;

    public function changeShippingMethod(): void;

    public function changeShippingMethodByStepLabel(): void;

    public function changeAddressByStepLabel(): void;

    public function hasNoAvailablePaymentMethodsWarning(): bool;

    public function isNextStepButtonUnavailable(): bool;

    public function getPaymentMethods(): array;
}
