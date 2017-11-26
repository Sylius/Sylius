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
    /**
     * @param string $shippingMethod
     */
    public function selectShippingMethod(string $shippingMethod): void;

    /**
     * @return string[]
     */
    public function getShippingMethods();

    /**
     * @return bool
     */
    public function hasNoShippingMethodsMessage(): bool;

    /**
     * @param string $shippingMethodName
     * @param string $fee
     *
     * @return bool
     */
    public function hasShippingMethodFee(string $shippingMethodName, string $fee): bool;

    /**
     * @param string $itemName
     *
     * @return string
     */
    public function getItemSubtotal(string $itemName): string;

    public function nextStep(): void;

    public function changeAddress(): void;

    public function changeAddressByStepLabel(): void;

    /**
     * @return mixed string
     */
    public function getPurchaserEmail();

    /**
     * @return string
     */
    public function getValidationMessageForShipment(): string;

    /**
     * @return bool
     */
    public function hasNoAvailableShippingMethodsWarning(): bool;

    /**
     * @return bool
     */
    public function isNextStepButtonUnavailable(): bool;

    /**
     * @param string $shippingMethodName
     *
     * @return bool
     */
    public function hasShippingMethod(string $shippingMethodName): bool;
}
