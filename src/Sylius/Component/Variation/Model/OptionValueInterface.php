<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Variation\Model;

use Sylius\Component\Resource\Model\CodeAwareInterface;
use Sylius\Component\Resource\Model\ResourceInterface;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
interface OptionValueInterface extends ResourceInterface, CodeAwareInterface
{
    /**
     * @return OptionInterface
     */
    public function getOption();

    /**
     * @param OptionInterface $option
     */
    public function setOption(OptionInterface $option = null);

    /**
     * Get internal value.
     *
     * @return string
     */
    public function getValue();

    /**
     * Set internal value.
     *
     * @param string $value
     */
    public function setValue($value);

    /**
     * Proxy method to access the name of real option object.
     * Those methods are mostly useful in templates so you can easily
     * display option name with their values.
     *
     * @return string The name of option
     */
    public function getName();

    /**
     * Proxy method to access the presentation of real option object.
     *
     * @return string The presentation of object
     */
    public function getPresentation();
}
