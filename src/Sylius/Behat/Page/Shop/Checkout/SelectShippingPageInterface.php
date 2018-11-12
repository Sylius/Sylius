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

namespace Sylius\Behat\Page\Shop\Checkout;

use Sylius\Behat\Page\SymfonyPageInterface;

interface SelectShippingPageInterface extends SymfonyPageInterface
{
    public function selectShippingMethod(string $shippingMethod);

    /**
     * @return string[]
     */
    public function getShippingMethods(): array;

    public function getSelectedShippingMethodName(): ?string;

    public function hasNoShippingMethodsMessage(): bool;

    public function hasShippingMethodFee(string $shippingMethodName, string $fee): bool;

    public function getItemSubtotal(string $itemName): string;

    public function nextStep();

    public function changeAddress();

    public function changeAddressByStepLabel();

    /**
     * @return mixed string
     */
    public function getPurchaserEmail();

    public function getValidationMessageForShipment(): string;

    public function hasNoAvailableShippingMethodsWarning(): bool;

    public function isNextStepButtonUnavailable(): bool;

    public function hasShippingMethod(string $shippingMethodName): bool;
}
