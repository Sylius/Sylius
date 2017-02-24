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
     * @param string $channelName
     */
    public function checkChannel($channelName);

    /**
     * @param string $description
     * @param string $languageCode
     */
    public function describeIt($description, $languageCode);

    /**
     * @param string $instructions
     * @param string $languageCode
     */
    public function setInstructions($instructions, $languageCode);

    /**
     * @param string $username
     */
    public function setPaypalGatewayUsername($username);

    /**
     * @param string $password
     */
    public function setPaypalGatewayPassword($password);

    /**
     * @param string $signature
     */
    public function setPaypalGatewaySignature($signature);

    /**
     * @param string $secretKey
     */
    public function setStripeSecretKey($secretKey);

    /**
     * @param string $publishableKey
     */
    public function setStripePublishableKey($publishableKey);

    /**
     * @return bool
     */
    public function isCodeDisabled();

    /**
     * @return bool
     */
    public function isPaymentMethodEnabled();
}
