<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Behat\Page\Admin\PaymentMethod;

use Sylius\Behat\Page\Admin\Crud\CreatePageInterface as BaseCreatePageInterface;

/**
 * @author Grzegorz Sadowski <grzegorz.sadowski@lakion.com>
 */
interface CreatePageInterface extends BaseCreatePageInterface
{
    public function enable();
    public function disable();

    /**
     * @param string $name
     * @param string $language
     */
    public function nameIt($name, $language);

    /**
     * @param string $code
     */
    public function specifyCode($code);

    /**
     * @param string $gateway
     */
    public function chooseGateway($gateway);

    /**
     * @param string $description
     * @param string $language
     */
    public function describeIt($description, $language);

    /**
     * @return bool
     */
    public function isCodeDisabled();

    /**
     * @return bool
     */
    public function isPaymentMethodEnabled();
}
