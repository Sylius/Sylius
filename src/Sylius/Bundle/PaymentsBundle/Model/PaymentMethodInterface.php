<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\PaymentsBundle\Model;

use Sylius\Bundle\ResourceBundle\Model\TimestampableInterface;

/**
 * Payment method interface.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
interface PaymentMethodInterface extends TimestampableInterface
{
    /**
     * Check whether the payments method is currently enabled.
     *
     * @return Boolean
     */
    public function isEnabled();

    /**
     * Enable or disable the payments method.
     *
     * @param Boolean $enabled
     */
    public function setEnabled($enabled);

    /**
     * Get payments method name.
     *
     * @return string
     */
    public function getName();

    /**
     * Set the name.
     *
     * @param string $name
     */
    public function setName($name);

    /**
     * Get payment method description.
     *
     * @return string
     */
    public function getDescription();

    /**
     * Set description.
     *
     * @param string $description
     */
    public function setDescription($description);

    /**
     * Set the payment gateway to use.
     *
     * @return string
     */
    public function getGateway();

    /**
     * Set gateway.
     *
     * @param string $gateway
     */
    public function setGateway($gateway);

    /**
     * Get the required app environment.
     *
     * @return string
     */
    public function getEnvironment();

    /**
     * Set the environment requirement.
     *
     * @param string $environment
     */
    public function setEnvironment($environment);
}
