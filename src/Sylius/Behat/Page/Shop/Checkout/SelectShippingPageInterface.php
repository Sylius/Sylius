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

interface SelectShippingPageInterface extends SymfonyPageInterface
{
    public function selectShippingMethod(string $shippingMethod): void;

    public function getShippingMethods(): array;

    public function getSelectedShippingMethodName(): ?string;

    public function hasNoShippingMethodsMessage(): bool;

    public function hasShippingMethodFee(string $shippingMethodName, string $fee): bool;

    public function getItemSubtotal(string $itemName): string;

    public function nextStep(): void;

    public function changeAddress(): void;

    public function changeAddressByStepLabel(): void;

    public function getPurchaserIdentifier(): string;

    public function getValidationMessageForShipment(): string;

    public function hasNoAvailableShippingMethodsWarning(): bool;

    public function isNextStepButtonUnavailable(): bool;

    public function hasShippingMethod(string $shippingMethodName): bool;
}
