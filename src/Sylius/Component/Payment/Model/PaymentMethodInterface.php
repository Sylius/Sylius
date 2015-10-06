<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Payment\Model;

use Sylius\Component\Resource\Model\TimestampableInterface;

/**
 * Payment method interface.
 *
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
interface PaymentMethodInterface extends TimestampableInterface
{
    /**
     * @return Boolean
     */
    public function isEnabled();

    /**
     * @param Boolean $enabled
     */
    public function setEnabled($enabled);

    /**
     * @return string
     */
    public function getName();

    /**
     * @param string $name
     */
    public function setName($name);

    /**
     * @return string
     */
    public function getDescription();

    /**
     * @param string $description
     */
    public function setDescription($description);

    /**
     * @return string
     */
    public function getGateway();

    /**
     * @param string $gateway
     */
    public function setGateway($gateway);

    /**
     * @return string
     */
    public function getEnvironment();

    /**
     * @param string $environment
     */
    public function setEnvironment($environment);

    /**
     * @return string
     */
    public function getFeeCalculator();

    /**
     * @param string $feeCalculator
     */
    public function setFeeCalculator($feeCalculator);

    /**
     * @return array
     */
    public function getFeeCalculatorConfiguration();

    /**
     * @param array $feeCalculatorConfiguration
     */
    public function setFeeCalculatorConfiguration(array $feeCalculatorConfiguration);
}
