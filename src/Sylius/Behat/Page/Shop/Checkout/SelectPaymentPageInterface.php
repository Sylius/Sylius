<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Behat\Page\Shop\Checkout;

use Sylius\Behat\Page\SymfonyPageInterface;

/**
 * @author Anna Walasek <anna.walasek@lakion.com>
 */
interface SelectPaymentPageInterface extends SymfonyPageInterface
{
    /**
     * @param string $paymentMethod
     */
    public function selectPaymentMethod($paymentMethod);
    
    /**
     * @param string $paymentMethodName
     *
     * @return bool
     */
    public function hasPaymentMethod($paymentMethodName);

    /**
     * @param string $itemName
     *
     * @return string
     */
    public function getItemSubtotal($itemName);

    public function nextStep();

    public function changeShippingMethod();

    public function changeShippingMethodByStepLabel();

    public function changeAddressByStepLabel();

    /**
     * @return bool
     */
    public function hasNoAvailablePaymentMethodsWarning();

    /**
     * @return bool
     */
    public function isNextStepButtonUnavailable();

    /**
     * @return string[]
     */
    public function getPaymentMethods();
}
