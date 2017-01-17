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
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 */
interface SelectShippingPageInterface extends SymfonyPageInterface
{
    /**
     * @param string $shippingMethod
     */
    public function selectShippingMethod($shippingMethod);

    /**
     * @return string[]
     */
    public function getShippingMethods();

    /**
     * @return bool
     */
    public function hasNoShippingMethodsMessage();

    /**
     * @param string $shippingMethodName
     * @param string $fee
     *
     * @return bool
     */
    public function hasShippingMethodFee($shippingMethodName, $fee);

    /**
     * @param string $itemName
     *
     * @return string
     */
    public function getItemSubtotal($itemName);

    public function nextStep();

    public function changeAddress();

    public function changeAddressByStepLabel();

    /**
     * @return mixed string
     */
    public function getPurchaserEmail();

    /**
     * @return string
     */
    public function getValidationMessageForShipment();

    /**
     * @return bool
     */
    public function hasNoAvailableShippingMethodsWarning();

    /**
     * @return bool
     */
    public function isNextStepButtonUnavailable();

    /**
     * @param string $shippingMethodName
     *
     * @return bool
     */
    public function hasShippingMethod($shippingMethodName);
}
