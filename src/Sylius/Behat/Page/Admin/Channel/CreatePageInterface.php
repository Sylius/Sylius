<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Behat\Page\Admin\Channel;

use Sylius\Behat\Page\Admin\Crud\CreatePageInterface as BaseCreatePageInterface;

/**
 * @author Łukasz Chruściel <lukasz.chrusciel@lakion.com>
 */
interface CreatePageInterface extends BaseCreatePageInterface
{
    public function enable();

    public function disable();

    /**
     * @param string $name
     */
    public function nameIt($name);

    /**
     * @param string $code
     */
    public function specifyCode($code);

    /**
     * @param string $description
     */
    public function describeItAs($description);

    /**
     * @param string $hostname
     */
    public function setHostname($hostname);

    /**
     * @param string $color
     */
    public function defineColor($color);

    /**
     * @param string $language
     */
    public function chooseLocale($language);

    /**
     * @param string $currency
     */
    public function chooseCurrency($currency);

    /**
     * @param string $shippingMethod
     */
    public function chooseShippingMethod($shippingMethod);

    /**
     * @param string $paymentMethod
     */
    public function choosePaymentMethod($paymentMethod);
}
