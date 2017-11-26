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

interface SelectPaymentPageInterface extends SymfonyPageInterface
{
    /**
     * @param string $paymentMethod
     */
    public function selectPaymentMethod(string $paymentMethod): void;

    /**
     * @param string $paymentMethodName
     *
     * @return bool
     */
    public function hasPaymentMethod(string $paymentMethodName): bool;

    /**
     * @param string $itemName
     *
     * @return string
     */
    public function getItemSubtotal(string $itemName): string;

    public function nextStep(): void;

    public function changeShippingMethod(): void;

    public function changeShippingMethodByStepLabel(): void;

    public function changeAddressByStepLabel(): void;

    /**
     * @return bool
     */
    public function hasNoAvailablePaymentMethodsWarning(): bool;

    /**
     * @return bool
     */
    public function isNextStepButtonUnavailable(): bool;

    /**
     * @return string[]
     */
    public function getPaymentMethods();
}
