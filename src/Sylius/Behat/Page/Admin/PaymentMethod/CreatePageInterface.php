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
     * @param string $languageCode
     */
    public function nameIt($name, $languageCode);

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
     * @param string $languageCode
     */
    public function describeIt($description, $languageCode);

    /**
     * @return bool
     */
    public function isCodeDisabled();

    /**
     * @return bool
     */
    public function isPaymentMethodEnabled();
}
