<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Shipping\Model;

use Doctrine\Common\Collections\Collection;
use Sylius\Component\Resource\Model\CodeAwareInterface;
use Sylius\Component\Resource\Model\TimestampableInterface;
use Sylius\Component\Resource\Model\ToggleableInterface;
use Sylius\Component\Resource\Model\TranslatableInterface;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 * @author Gonzalo Vilaseca <gvilaseca@reiss.co.uk>
 */
interface ShippingMethodInterface extends
    CodeAwareInterface,
    ShippingMethodTranslationInterface,
    TimestampableInterface,
    ToggleableInterface,
    TranslatableInterface
{
    /**
     * Get calculator name assigned for this shipping method.
     *
     * @return string
     */
    public function getCalculator();

    /**
     * Set calculator name assigned for this shipping method.
     *
     * @param string $calculator
     */
    public function setCalculator($calculator);

    /**
     * Get any extra configuration for calculator.
     *
     * @return array
     */
    public function getConfiguration();

    /**
     * Set extra configuration for calculator.
     *
     * @param array $configuration
     */
    public function setConfiguration(array $configuration);
}
