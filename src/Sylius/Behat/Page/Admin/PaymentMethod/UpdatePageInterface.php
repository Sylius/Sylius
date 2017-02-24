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

use Sylius\Behat\Page\Admin\Crud\UpdatePageInterface as BaseUpdatePageInterface;

/**
 * @author Arkadiusz Krakowiak <arkadiusz.krakowiak@lakion.com>
 */
interface UpdatePageInterface extends BaseUpdatePageInterface
{
    public function enable();
    public function disable();

    /**
     * @param string $name
     * @param string $languageCode
     */
    public function nameIt($name, $languageCode);

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
     * @return bool
     */
    public function isCodeDisabled();

    /**
     * @return bool
     */
    public function isFactoryNameFieldDisabled();

    /**
     * @return bool
     */
    public function isPaymentMethodEnabled();

    /**
     * @param string $channelName
     *
     * @return bool
     */
    public function isAvailableInChannel($channelName);

    /**
     * @param string $language
     *
     * @return string
     */
    public function getPaymentMethodInstructions($language);
}
