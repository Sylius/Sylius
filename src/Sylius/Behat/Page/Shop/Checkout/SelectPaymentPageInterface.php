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
    public function selectPaymentMethod(string $paymentMethod);

    public function hasPaymentMethod(string $paymentMethodName): bool;

    public function getItemSubtotal(string $itemName): string;

    public function nextStep();

    public function changeShippingMethod();

    public function changeShippingMethodByStepLabel();

    public function changeAddressByStepLabel();

    public function hasNoAvailablePaymentMethodsWarning(): bool;

    public function isNextStepButtonUnavailable(): bool;

    /**
     * @return string[]
     */
    public function getPaymentMethods(): array;
}
